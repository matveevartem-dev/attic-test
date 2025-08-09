<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

interface CacheInterface
{
    /**
     * Creates a connection to the cache server
     * @return static
     */
    public function connect(): static;

    /**
     * Returns the value for the key
     * @param string $key
     * @param mixed $default
     * @return array|string
     */
    public function get(string $key, mixed $default = null): array|string;

    /**
     * Checks for the presence of a key
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Store value with key on cache server
     * @param string $key
     * @param array|string $value
     * @param int|null $ttl key TTL
     * @return bool
     */
    public function set(string $key, array|string $value, ?int $ttl = null): bool;
}
