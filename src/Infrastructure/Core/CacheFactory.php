<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use App\Infrastructure\Core\CacheInterface;
use App\Infrastructure\Core\RedisCache;
use Redis;

final class CacheFactory implements CacheFactoryInterface
{
    public function create(): CacheInterface
    {
        $cache = new RedisCache(new Redis());
        $cache->connect();

        return $cache;
    }
}
