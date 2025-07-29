<?php

declare(strict_types=1);

namespace App\Http\Action\Search;

use App\Model\Search\SearchRepository;
use HttpSoft\Basis\Exception\NotFoundHttpException;
use HttpSoft\Basis\Exception\BadRequestHttpException;
use HttpSoft\Basis\Response\PrepareJsonDataTrait;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SearchAction implements RequestHandlerInterface
{
    use PrepareJsonDataTrait;

    /**
     * @param SearchRepository $searchRepository
     */
    public function __construct(private SearchRepository $searchRepository)
    {
    }

    /**
     * {@inheritDoc}
     * @throws NotFoundHttpException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];
        $queryParams = $request->getQueryParams();
        if (empty($queryParams)) {
            throw new BadRequestHttpException('Empty request body');
        }

        if (!isset($queryParams['need'])) {
            throw new BadRequestHttpException('The body must contain the `need` parameter');
        }

        $need = $queryParams['need'];
        if (strlen($need) < 3) {
            throw new BadRequestHttpException('The length of the `need` parameter must be 3 or more.');
        }

        $data = $this->searchRepository->search($need);

        return new JsonResponse($this->prepareJsonData($data));
    }
}
