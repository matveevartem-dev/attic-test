<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use App\Infrastructure\Core\CacheInterface;
use App\Infrastructure\Core\RedisCache;

final class CacheFactory implements CacheFactoryInterface
{
    public function create(): CacheInterface
    {
        return (new RedisCache())->connect();
    }
}
