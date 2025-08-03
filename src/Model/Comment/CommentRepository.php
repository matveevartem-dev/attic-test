<?php

declare(strict_types=1);

namespace App\Model\Comment;

use App\Model\Comment\Comment;
use App\Infrastructure\Core\DatabaseInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use HttpSoft\Basis\Exception\NotFoundHttpException;
use PDO;
use Psr\Container\ContainerInterface;

final class CommentRepository
{
    const BATCH_SIZE = 100;

    /**
     * @var EntityManager|PDO
     */
    private EntityManager|PDO $connection;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(private ContainerInterface $container, private DatabaseInterface $db)
    {
        $this->connection = $db->getConnecion();
    }

    /**
     * @return Comment[]
     */
    public function findAll(): array
    {
        if ($this->connection instanceof PDO) {
            return $this->findAllPdo();
        }

        if ($this->connection instanceof EntityManager) {
            return $this->findAllDoctrine();
        }

        throw new \Exception();
    }

    /**
     * @return Comment[]
     */
    private function findAllPdo(): array
    {
        $query = <<<SQL
            SELECT
                `post_id`,
                `id`,
                `email`,
                `name`,
                `body`
            FROM comment;
        SQL;

        $stm = $this->connection->prepare($query, []);
        $stm->setFetchMode(PDO::FETCH_ASSOC);
        $stm->execute();

        $comments = [];
        foreach ($stm->fetchAll() as $row) {
            $comments[] = new Comment(...array_values($row));
        }

        return $comments;
    }

    /**
     * @return Comment[]
     */
    private function findAllDoctrine(): array
    {
        $query = <<<SQL
            SELECT * FROM comment;
        SQL;

        $stm = $this->connection->prepare($query, []);
        $stm->execute();

        $comments = $stm->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $comments;
    }

    /**
     * @param int $id
     * @return Comment
     */
    public function findById(int $id): Comment
    {
        if ($this->connection instanceof PDO) {
            return $this->findByIdPdo($id);
        }

        if ($this->connection instanceof EntityManager) {
            return $this->findByIdDoctrine($id);
        }

        throw new \Exception(get_class($this->connection));
    }

    /**
     * @see PostRepository::findById()
     */
    private function findByIdPdo(int $id): Comment
    {
        $query = <<<SQL
            SELECT
                `post_id`,
                `id`,
                `email`,
                `name`,
                `body`
            FROM comment
            WHERE `id` = :id
        SQL;

        $stm = $this->connection->prepare($query, []);
        $stm->setFetchMode(PDO::FETCH_ASSOC);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        $data = $stm->fetch();

        if ($data === false) {
            throw new NotFoundHttpException(get_class($this->connection));
        }

        $comment = new Comment(...array_values(
            $data
        ));

        return $comment;
    }

    /**
     * @see PostRepository::findById()
     */
    private function findByIdDoctrine(int $id): Comment
    {
        $post = $this->connection->createQueryBuilder()
                ->select('c')
                ->from(Comment::class, 'c')
                ->where(['id' => $id])
                ->getQuery()
                ->execute();

        return $post;
    }

    /**
     * Saves one comment to the database
     *
     * @param Comment $Comment
     * @return bool
     * @throws \PDOException
     * @throws \RuntimeException
     */
    public function saveOne(Comment $comment): bool
    {
        $data = [$comment];

        return (bool) $this->save($data);
    }

    /**
     * Saves an array of comments in the database
     *
     * @param array<Comment> $data
     * @return int number of rows inserted
     * @throws \PDOException
     * @throws \RuntimeException
     */
    public function saveMany(array $data): int
    {
        return (int) $this->save($data);
    }

    /**
     * Saves one or more comments to the database
     *
     * @param array<Comment> $data
     * @throws \RuntimeException
     * @return int number of rows inserted
     */
    private function save(array $data): int
    {
        if ($this->connection instanceof PDO) {
            return $this->insertPdo($data);
        }

        if ($this->connection instanceof EntityManager) {
            return $this->insertDoctrine($data);
        }

        throw new \RuntimeException();
    }

    /**
     * @param array<Comment> $data
     * @return int
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    private function insertPdo(array $data): int
    {
        $counter = 0;
        $query = <<<SQL
            INSERT INTO `comment` (`id`, `post_id`, `email`, `name`, `body`)
            VALUES (:id, :postId, :email, :name, :body)
        SQL;

        $this->connection->beginTransaction();

        $stm = $this->connection->prepare($query);

        foreach ($data as $i => $item) {
            if (!$item instanceof Comment) {
                $this->connection->rollBack();
                throw new \InvalidArgumentException();
            }

            $stm->bindValue(':id', $item->getId(), PDO::PARAM_INT);
            $stm->bindValue(':postId', $item->getPostId(), PDO::PARAM_INT);
            $stm->bindValue(':email', $item->getEmail(), PDO::PARAM_STR);
            $stm->bindValue(':name', $item->getName(), PDO::PARAM_STR);
            $stm->bindValue(':body', $item->getBody(), PDO::PARAM_STR);

            $counter += (int) $stm->execute();

            if ($i+1 % self::BATCH_SIZE === 0) {
                $this->connection->commit();
                $this->connection->beginTransaction();
            }
        }

        $this->connection->commit();

        return $counter;
    }

    /**
     * @param array<Comment> $data
     * @return int number of rows
     * @throws \Exception
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    private function insertDoctrine(array $data): int
    {
        $counter = 0;
        $rsm = new ResultSetMapping();

        $this->connection->createNativeQuery("SET autocommit=0", $rsm)->execute();
        $this->connection->createNativeQuery("SET unique_checks=0", $rsm)->execute();
        $this->connection->createNativeQuery("SET foreign_key_checks=0", $rsm)->execute();

        foreach ($data as $i => $item) {
            if (!$item instanceof Comment) {
                $this->connection->rollBack();
                throw new \InvalidArgumentException();
            }
            $this->connection->persist($item);

            if ($i+1 % self::BATCH_SIZE === 0) {
                $this->connection->flush();
                $this->connection->clear();
            }
            $counter++;
        }

        $this->connection->flush();
        $this->connection->clear();

        $this->connection->createNativeQuery("SET foreign_key_checks=1", $rsm)->execute();
        $this->connection->createNativeQuery("SET unique_checks=1", $rsm)->execute();
        $this->connection->createNativeQuery("SET autocommit=1", $rsm)->execute();

        return $counter;
    }
}
