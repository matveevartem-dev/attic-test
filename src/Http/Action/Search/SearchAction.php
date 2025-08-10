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

    private int $per_page;

    /**
     * @param CacheInterface $cache
     * @param SearchRepository $searchRepository
     */
    public function __construct(
        private CacheInterface $cache,
        private SearchRepository $searchRepository
    ) {
        $this->per_page = (int) ($_ENV['VITE_PAGE_SIZE'] ?? 1000);
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
        if (strlen($need) < $_ENV['VITE_MIN_SEARCH_LENGTH']) {
            throw new BadRequestHttpException('The length of the `need` parameter must be ' . $_ENV['VITE_MIN_SEARCH_LENGTH'] . ' or more.');
        }

        $page = (int) ($queryParams['page'] ?? 1);

        $hash = md5($page . $need);
        if  ($this->cache->has($hash)) {
            $data = $this->cache->get($hash);
        } else {
            $data = $this->searchRepository->search($need, $this->offset($page), $this->per_page);
            if (!empty($data)) {
                $this->cache->set($hash, $data);
            }
        }

        return new JsonResponse($this->prepareJsonData($data));
    }

    /**
     * Calculates LIMIT OFFSET for current page
     * @param int $page page number, minimum 1
     * @return int
     */
    private function offset(int $page): int
    {
        if ($page < 1) {
            throw new BadRequestHttpException('The page number must be greater than one');
        }

        return ($page - 1) * $this->per_page;
    }
}
