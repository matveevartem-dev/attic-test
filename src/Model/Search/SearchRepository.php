<?php

declare(strict_types=1);

namespace App\Model\Search;

use App\Infrastructure\Core\DatabaseInterface;
use App\Model\Search\SearchResult;
use Doctrine\ORM\EntityManager;
use PDO;

use function array_values;

final class SearchRepository
{
    private EntityManager|PDO $connection;

    public function __construct(private DatabaseInterface $db)
    {
        $this->connection = $db->getConnecion();
    }

    /**
     * @param string $need search word
     * @param int $offset search offset, default 0
     * @param int $limit search limit, by default this is the maximum value of the int type
     * @return SearchResult[]
     */
    public function search(
        string $need,
        int $offset = 0,
        int $limit = PHP_INT_MAX
    ): array {
        $query = <<<SQL
            SELECT
                c.post_id pid,
                p.title title,
                i.email email,
                JSON_OBJECTAGG(
                    c.id,
                    JSON_OBJECT('id', c.id, 'email', c.email, 'name', c.name, 'body', c.body)
                ) comments
            FROM comment c
            JOIN post p
                ON p.id = c.post_id
            JOIN identity i
                ON i.uid = p.user_id
            WHERE
                MATCH (c.body) AGAINST (:need IN BOOLEAN MODE)
            GROUP BY c.post_id
            LIMIT :offset, :limit;
        SQL;

        $stm = $this->connection->prepare($query);
        $stm->bindParam(':need', $need, PDO::PARAM_STR);
        $stm->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stm->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stm->setFetchMode(PDO::FETCH_OBJ);
        $stm->execute();

        $data = [];
        while ($item = $stm->fetch()) {
            $data[] = SearchResult::fromObject($item);
        }

        return array_values($data);
    }
}
