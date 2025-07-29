<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use App\Infrastructure\Core\ConfigInterface;

final class Config implements ConfigInterface
{
    public function __construct(private ?array $config = null)
    {
        $this->config ??= require __DIR__ . '/../../../config/config.php';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }
}
