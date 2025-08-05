<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use App\Infrastructure\Core\CacheInterface;

interface CacheFactoryInterface
{
    public function create(): CacheInterface;
}
