<?php

declare(strict_types=1);

namespace App\Http\Action\Search;

use App\Infrastructure\Core\CacheInterface;
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
     * @param CacheInterface $cache
     * @param SearchRepository $searchRepository
     */
    public function __construct(
        private CacheInterface $cache,
        private SearchRepository $searchRepository
    ) {
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

        $hash = md5($need);
        if  ($this->cache->has($hash)) {
            $data = $this->cache->get($hash);
        } else {
            $data = $this->searchRepository->search($need);
            if (!empty($data)) {
                $this->cache->set($hash, $data);
            }
        }

        return new JsonResponse($this->prepareJsonData($data));
    }
}
