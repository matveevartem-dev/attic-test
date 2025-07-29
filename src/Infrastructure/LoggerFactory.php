<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Infrastructure\Core\ConfigInterface;
use Devanych\Di\FactoryInterface;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class LoggerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @return LoggerInterface
     * @throws Exception
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress DeprecatedConstant
     */

    public function __construct(private ConfigInterface $config)
    {
    }

    public function create(ContainerInterface $container): LoggerInterface
    {
        $logger = new Logger('App');

        $logger->pushHandler(new StreamHandler(
            $this->config->get('log_file'),
                $this->config->get('debug') ? Logger::DEBUG : Logger::WARNING
        ));

        return $logger;
    }
}
