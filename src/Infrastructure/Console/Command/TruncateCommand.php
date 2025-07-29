<?php

declare(strict_types= 1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Core\Container;
use App\Infrastructure\Core\ConnectionTypeEnum;
use App\Infrastructure\Core\DatabaseFactoryInterface;
use PDO;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


final class TruncateCommand extends Command
{
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
        $this->setName('truncate')
            ->setDescription('Truncates tables of posts and comments')
            ->setHelp('TRUNCATE TABLE comment; TRUNCATE TABLE post;');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->truncateComments($output);
        $this->truncatePosts($output);

        return self::SUCCESS;
    }

    private function truncatePosts(OutputInterface $output)
    {
        $query = <<<SQL
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE `post`;
            SET FOREIGN_KEY_CHECKS = 1;
        SQL;

        $result = $this->connection->prepare($query)->execute();
        if (false === $result) {
            throw new \PDOException(sprintf('SQL query execution error: ', $query));
        }

        $output->writeln("The post table was trunctated");
    }

    private function truncateComments(OutputInterface $output)
    {
        $query = <<<SQL
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE `comment`;
            SET FOREIGN_KEY_CHECKS = 1;
        SQL;

        $result = $this->connection->prepare($query)->execute();
        if (false === $result) {
            throw new \PDOException(sprintf('SQL query execution error: ', $query));
        }

        $output->writeln("The comment table was trunctated");
    }
}
