<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use Devanych\Di\Container as BaseContainer;
use Psr\Container\ContainerInterface;
use function PHPUnit\Framework\isInstanceOf;

final class Container implements ContainerInterface
{
    private const DEFAULT_DI_VALUES = __DIR__ . '/../../../config/di.php';
    private BaseContainer $container;

    public function __construct(array $definitions = [], bool $useCache = true)
    {
        $definitions = $definitions ?: $this->getDefaultDefinitions();
        $this->container = new BaseContainer($definitions);
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * Returns default DI settings
     * @return array
     */
    private function getDefaultDefinitions(): array
    {
        return
            require self::DEFAULT_DI_VALUES;
    }
}
