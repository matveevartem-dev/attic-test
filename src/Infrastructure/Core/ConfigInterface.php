<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

interface ConfigInterface
{
    /**
     * @param array|null $config if config is null, default values from di.php will be used
     */
    public function __construct(?array $config = null);

    /**
     * Returns the value for the key
     *
     * @param string $key
     * @param mixed $default
     * @return void
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Sets the value of the key
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void;

    /**
     * Checks if the key is in the container
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;
}
