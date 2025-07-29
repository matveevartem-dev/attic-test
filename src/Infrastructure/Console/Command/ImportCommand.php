<?php

declare(strict_types= 1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Parser\FlatParserListener;
use App\Model\Comment\Comment;
use App\Model\Comment\CommentRepository;
use App\Model\Post\Post;
use App\Model\Post\PostRepository;
use App\Infrastructure\Core\Container;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JsonStreamingParser\Parser;

final class ImportCommand extends Command
{
    private const URL_POST = 'https://jsonplaceholder.typicode.com/posts';
    private const URL_COMMENT = 'https://jsonplaceholder.typicode.com/comments';

    private ContainerInterface $container;

    /**
     * {@inheritDoc}
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->container = new Container();
    }

    protected function configure()
    {
        $this->setName('import')
            ->setDescription('Imports data from url\'s')
            ->setHelp('Imports data from ' . implode(', ', [
                self::URL_POST,
                self::URL_COMMENT
            ]));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->importPosts($output);
        $this->importComments($output);

        return self::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        return self::SUCCESS;
    }

    private function importPosts(OutputInterface $output)
    {
        $repository = $this->container->get(PostRepository::class);

        $counter = 0;
        $stream = fopen(self::URL_POST, 'r');

        $listener = new FlatParserListener(Post::class, function(array $data) use ($repository, &$counter) {
            $counter += $repository->saveMany($data);
        });

        $parser = new Parser($stream, $listener);
        $parser->parse();

        fclose($stream);

        $output->writeln("Loaded {$counter} posts (Загружено {$counter} постов)");
    }

    private function importComments(OutputInterface $output)
    {
        $repository = $this->container->get(CommentRepository::class);

        $counter = 0;
        $stream = fopen(self::URL_COMMENT, 'r');

        $listener = new FlatParserListener(Comment::class, function(array $data) use ($repository, &$counter) {
            $counter += $repository->saveMany($data);
        });

        $parser = new Parser($stream, $listener);
        $parser->parse();

        fclose($stream);

        $output->writeln("Loaded {$counter} comments (Загружено {$counter} комментариев)");
    }
}
