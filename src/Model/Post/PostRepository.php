<?php

declare(strict_types=1);

namespace App\Model\Post;

use App\Model\Post\Post;
use App\Infrastructure\Core\DatabaseInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use HttpSoft\Basis\Exception\NotFoundHttpException;
use PDO;
use Psr\Container\ContainerInterface;

final class PostRepository
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
     * @return Post[]
     */
    public function findAll(): array
    {
        if ($this->connection instanceof PDO) {
            return $this->findAllPdo();
        }

        if ($this->connection instanceof EntityManager) {
            return $this->findAllDoctrine();
        }

        throw new \Exception(get_class($this->connection));
    }

    /**
     * @see PostRepository::findAll()
     */
    private function findAllPdo(): array
    {
        $query = <<<SQL
            SELECT
                `user_id`,
                `id`,
                `title`,
                `body`
            FROM post;
        SQL;

        $stm = $this->connection->prepare($query, []);
        $stm->setFetchMode(PDO::FETCH_ASSOC);
        $stm->execute();

        $posts = [];
        foreach ($stm->fetchAll() as $row) {
            $posts[] = new Post(...array_values($row));
        }

        return $posts;
    }

    /**
     * @see PostRepository::findAll()
     */
    private function findAllDoctrine(): array
    {
        $posts = $this->connection->createQueryBuilder()
                ->select('p')
                ->from(Post::class, 'p')
                ->getQuery()
                ->execute();

        return $posts;
    }

    /**
     * @param int $id
     * @return Post
     */
    public function findById(int $id): Post
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
    private function findByIdPdo(int $id): Post
    {
        $query = <<<SQL
            SELECT
                `user_id`,
                `id`,
                `title`,
                `body`
            FROM post
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

        $post = new Post(...array_values(
            $data
        ));

        return $post;
    }

    /**
     * @see PostRepository::findById()
     */
    private function findByIdDoctrine(int $id): Post
    {
        $post = $this->connection->createQueryBuilder()
                ->select('p', )
                ->from(Post::class, 'p')
                ->where(['id' => $id])
                ->getQuery()
                ->execute();

        return $post;
    }

    /**
     * Saves one post to the database
     *
     * @param Post $post
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \PDOException
     * @return bool
     */
    public function saveOne(Post $post): bool
    {
        $data = [$post];

        return (bool) $this->save($data);
    }

    /**
     * Saves an array of posts in the database
     *
     * @param array<Post> $data
     * @return int
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function saveMany(array $data): int
    {
        return (int) $this->save($data);
    }

    /**
     * Saves one or more posts to the database
     *
     * @param array<Post> $data
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \PDOException
     * @return int
     */
    private function save(array $data): int
    {
        if ($this->connection instanceof PDO) {
            return $this->insertPdo($data);
        }

        if ($this->connection instanceof EntityManager) {
            return $this->insertDoctrine($data);
        }

        throw new \Exception(get_class($this->connection));
    }

    /**
     * @param array<Post> $data
     * @return int
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    private function insertPdo(array $data): int
    {
        $counter = 0;
        $query = <<<SQL
            INSERT INTO `post` (`id`, `user_id`, `title`, `body`)
            VALUES (:id, :userId, :title, :body)
        SQL;

        $this->connection->beginTransaction();

        $stm = $this->connection->prepare($query);

        foreach ($data as $i => $item) {
            if (!$item instanceof Post) {
                $this->connection->rollBack();
                throw new \InvalidArgumentException();
            }

            $stm->bindValue(':id', $item->getId(), PDO::PARAM_INT);
            $stm->bindValue(':userId', $item->getUserId(), PDO::PARAM_INT);
            $stm->bindValue(':title', $item->getTitle(), PDO::PARAM_STR);
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
     * @param array<Post> $data
     * @return int number of rows
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    private function insertDoctrine(array $data): int
    {
        $rsm = new ResultSetMapping();

        $this->connection->createNativeQuery("SET autocommit=0", $rsm)->execute();
        $this->connection->createNativeQuery("SET unique_checks=0", $rsm)->execute();
        $this->connection->createNativeQuery("SET foreign_key_checks=0", $rsm)->execute();

        $counter = 0;

        foreach ($data as $i => $item) {
            if (!$item instanceof Post) {
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
