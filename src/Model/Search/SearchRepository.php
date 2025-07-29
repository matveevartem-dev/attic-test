<?php

declare(strict_types=1);

namespace App\Model\Search;

use App\Model\Post\Post;
use App\Infrastructure\Core\DatabaseInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PDO;
use Psr\Container\ContainerInterface;

use App\Model\Search\SearchResult;
use Elasticsearch\Endpoints\Eql\Search;

use function array_values;

final class SearchRepository
{
    /**
     * @var array<int, SearchResult>
     */
    private EntityManager|PDO $connection;

    /**
     * @param array<int, SearchResult>|null $comments
     */
    public function __construct(private ContainerInterface $container, private DatabaseInterface $db)
    {
        $this->connection = $db->getConnecion();
    }

    /**
     * @return SearchResult[]
     */
    public function search(string $need): array
    {
        $query = <<<SQL
            SELECT
            c.post_id pid,
            p.title,
            JSON_OBJECTAGG(
                c.id,
                JSON_OBJECT('id', c.id, 'email', c.email, 'name', c.name, 'body', c.body)
            ) comments
            FROM comment c
            JOIN post p
            ON p.id = c.post_id
            WHERE
            MATCH (c.body) AGAINST (:need IN BOOLEAN MODE)
            GROUP BY c.post_id;
        SQL;

        $stm = $this->connection->prepare($query);
        $stm->bindParam(':need', $need, PDO::PARAM_STR);
        $stm->setFetchMode(PDO::FETCH_ASSOC);
        $stm->execute();

        $data = [];

        while ($item = $stm->fetch()) {
            $data[] = $this->toPdo($item);
        }

        return array_values($data);
    }

    private function toPdo(array $item)
    {
        return [
            'id' => $item['pid'],
            'title' => $item['title'],
            'comments' => json_decode($item['comments'])
        ];
    }
}
