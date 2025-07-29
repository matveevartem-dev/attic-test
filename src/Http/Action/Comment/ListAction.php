<?php

declare(strict_types=1);

namespace App\Http\Action\Comment;

use App\Model\Comment\CommentRepository;
use HttpSoft\Basis\Response\PrepareJsonDataTrait;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListAction implements RequestHandlerInterface
{
    use PrepareJsonDataTrait;

    /**
     * @param CommentRepository $commentRepository
     */
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $jsonData = $this->commentRepository->findAll();

        return new JsonResponse($this->prepareJsonData($jsonData));
    }
}
