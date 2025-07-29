<?php

declare(strict_types= 1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Core\Container;
use App\Infrastructure\Core\ConnectionTypeEnum;
use App\Infrastructure\Core\DatabaseFactoryInterface;
use PDO;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

final class MigrateCommand extends Command
{
    private const int ADDED_USERS = 20;

    private PDO|EntityManager $connection;

    /**
     * {@inheritDoc}
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $container = new Container();
        $databaseFactory = $container->get(DatabaseFactoryInterface::class);
        $this->connection = $databaseFactory->create(ConnectionTypeEnum::PDO_MODE);
    }

    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Creates tables of users, posts and comments')
            ->setHelp('CREATE TABLE `identity`; CREATE TABLE post; CREATE TABLE comment;');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createIdentity($output);
        $this->createPosts($output);
        $this->createComments($output);
        $this->addUsers($output);

        return self::SUCCESS;
    }

    private function createIdentity(OutputInterface $output)
    {
        $query = <<<SQL
            CREATE TABLE IF NOT EXISTS `identity` (
                `id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL,
                `uid` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `identity_uid` (`uid`),
            UNIQUE KEY `identity_email` (`email`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        $result = $this->connection->prepare($query)->execute();
        if (false === $result) {
            throw new \PDOException(sprintf('SQL query execution error: ', $query));
        }

        $output->writeln("The `identity` table was created");
    }

    private function createPosts(OutputInterface $output)
    {
        $query = <<<SQL
            CREATE TABLE IF NOT EXISTS `post` (
                `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `title` VARCHAR(200) COLLATE utf8mb4_general_ci,
                `body` TEXT COLLATE utf8mb4_general_ci,
            PRIMARY KEY (`id`),
                FOREIGN KEY (`user_id`)
                REFERENCES `identity`(`uid`)
                ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        $result = $this->connection->prepare($query)->execute();
        if (false === $result) {
            throw new \PDOException(sprintf('SQL query execution error: ', $query));
        }

        $output->writeln("The `post` table was created");
    }

    private function createComments(OutputInterface $output)
    {
        $query = <<<SQL
            CREATE TABLE IF NOT EXISTS `comment` (
                `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
                `post_id` INT UNSIGNED NOT NULL,
                `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
                `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
                `body` text COLLATE utf8mb4_general_ci,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`post_id`)
            REFERENCES `post`(`id`)
                ON UPDATE CASCADE ON DELETE RESTRICT,
            -- Для поиска слова только в теле комментария
            FULLTEXT KEY `body_ft` (`body`) WITH PARSER ngram 
            -- Для поиска слова в заголовке и теле комментария
            -- FULLTEXT KEY name_body_ft (`name`, `body`) WITH PARSER ngram 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        $result = $this->connection->prepare($query)->execute();
        if (false === $result) {
            throw new \PDOException(sprintf('SQL query execution error: ', $query));
        }

        $output->writeln("The `comment` table was created");
    }

    private function addUsers(OutputInterface $output)
    {
        $tm = strval(time());
        $query = <<<SQL
            -- DROP PROCEDURE IF EXISTS add_users_{$tm};
            -- DELIMITER $$
            CREATE PROCEDURE add_users_{$tm}()
            BEGIN
                DECLARE idx int unsigned;
                SET idx = 0;
                item: LOOP
                    SET idx = idx +1;
                    INSERT INTO `identity` (`id`, `email`, `password`)
                    VALUES (UUID(), CONCAT("user", idx, "@e.mail"), LEFT(MD5(RAND()), 32));
                    IF idx > (:max_user - 1) THEN
                        LEAVE item;
                    END IF;
                END LOOP item;
            END;
            -- $$
            -- DELIMITER
            CALL add_users_{$tm}();
        SQL;

        $stm = $this->connection->prepare($query);
        $stm->bindValue(':max_user', self::ADDED_USERS);
        $result = $stm->execute();

        if (false === $result) {
            throw new \PDOException(sprintf('SQL query execution error: ', $query));
        }

        $output->writeln("Added users: " . self::ADDED_USERS);
    }
}
