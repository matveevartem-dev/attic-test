<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Model\Post\PostRepository;
use App\Infrastructure\Core\DatabaseInterface;
use HttpSoft\Response\JsonResponse;
use HttpSoft\Response\HtmlResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class HomeAction implements RequestHandlerInterface
{
    public function __construct(private ContainerInterface $container, private DatabaseInterface $database)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $repo = $this->container->get(PostRepository::class);

        $loader = new \Twig\Loader\FilesystemLoader();
        $loader->setPaths(__DIR__ . '/../../../resource/View');
        $twig = new \Twig\Environment($loader);

        return new HtmlResponse($twig->render('index.html'));
    }
}
