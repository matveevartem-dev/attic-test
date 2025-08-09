<?php

declare(strict_types=1);

namespace App\Http\Action\Post;

use App\Model\Post\PostRepository;
use HttpSoft\Basis\Response\PrepareJsonDataTrait;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListAction implements RequestHandlerInterface
{
    use PrepareJsonDataTrait;

    /**
     * @param PostRepository $posts
     */
    public function __construct(private PostRepository $postRepository)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $jsonData = $this->postRepository->findAll();

        return new JsonResponse($this->prepareJsonData($jsonData));
    }
}
