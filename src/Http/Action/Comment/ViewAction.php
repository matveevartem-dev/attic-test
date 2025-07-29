<?php

declare(strict_types=1);

namespace App\Http\Action\Comment;

use App\Model\Comment\CommentRepository;
use HttpSoft\Basis\Exception\NotFoundHttpException;
use HttpSoft\Basis\Response\PrepareJsonDataTrait;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ViewAction implements RequestHandlerInterface
{
    use PrepareJsonDataTrait;

    /**
     * @param CommentRepository $posts
     */
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    /**
     * {@inheritDoc}
     * @throws NotFoundHttpException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$comment = $this->commentRepository->findById((int) $request->getAttribute('id'))) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($this->prepareJsonData($comment));
    }
}
