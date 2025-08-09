<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use App\Infrastructure\Core\CacheInterface;
use Redis;

final class RedisCache implements CacheInterface
{
    private const int TTL = 10;

    public function __construct(private ?Redis $redis = null)
    {
        $this->redis ??= new Redis();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, mixed $default = null): array|string
    {
        $result = $this->redis->get($key);
        if ($result === false) {
            return $default;
        }

        return json_decode($result, true);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, array|string $value, ?int $ttl = null): bool
    {
        $ttl ??= ((int) $_ENV['REDIS_TTL'] ?? self::TTL);

        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }

        return (bool) $this->redis->setex($key, $ttl, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(): static
    {
        if (!$this->redis->isConnected()) {
            $this->redis->connect($_ENV['REDIS_HOST'], (int) $_ENV['REDIS_PORT']);
            $this->redis->auth([$_ENV['REDIS_USER'], $_ENV['REDIS_PASSWORD']]);
            $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
        }

        return $this;
    }
}
