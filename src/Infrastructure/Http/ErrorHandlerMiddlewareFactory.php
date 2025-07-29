<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Infrastructure\Core\ConfigInterface;
use Devanych\Di\FactoryInterface;
use HttpSoft\Basis\ErrorHandler\ErrorJsonResponseGenerator;
use HttpSoft\Basis\ErrorHandler\LogErrorListener;
use HttpSoft\ErrorHandler\ErrorHandlerMiddleware;
use Psalm\Issue\ConfigIssue;
use PSpell\Config;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class ErrorHandlerMiddlewareFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @return ErrorHandlerMiddleware
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     */
    public function create(ContainerInterface $container): ErrorHandlerMiddleware
    {
        $errorHandler = new ErrorHandlerMiddleware(new ErrorJsonResponseGenerator($container->get(ConfigInterface::class)->get('debug')));
        $errorHandler->addListener(new LogErrorListener($container->get(LoggerInterface::class)));
        return $errorHandler;
    }
}
