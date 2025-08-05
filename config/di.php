<?php

declare(strict_types=1);

use App\Infrastructure\Http\ApplicationFactory;
use App\Infrastructure\Http\ErrorHandlerMiddlewareFactory;
use App\Infrastructure\LoggerFactory;
use App\Infrastructure\Core\Config;
use App\Infrastructure\Core\CacheFactory;
use App\Infrastructure\Core\CacheInterface;
use App\Infrastructure\Core\ConfigInterface;
use App\Infrastructure\Core\Container;
use App\Infrastructure\Core\Database;
use App\Infrastructure\Core\DatabaseInterface;
use App\Infrastructure\Core\DatabaseFactory;
use App\Infrastructure\Core\DatabaseFactoryInterface;
use HttpSoft\Basis\Application;
use HttpSoft\Basis\Response\CustomResponseFactory;
use HttpSoft\Cookie\CookieManager;
use HttpSoft\Cookie\CookieManagerInterface;
use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Emitter\EmitterInterface;
use HttpSoft\ErrorHandler\ErrorHandlerMiddleware;
use HttpSoft\Router\RouteCollector;
use HttpSoft\Runner\MiddlewarePipeline;
use HttpSoft\Runner\MiddlewarePipelineInterface;
use HttpSoft\Runner\MiddlewareResolver;
use HttpSoft\Runner\MiddlewareResolverInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

return [
    Application::class => ApplicationFactory::class,
    EmitterInterface::class => SapiEmitter::class,
    CacheInterface::class => fn() => (new CacheFactory())->create(),
    ContainerInterface::class => Container::class,
    ConfigInterface::class => Config::class,
    DatabaseInterface::class => Database::class,
    DatabaseFactoryInterface::class => DatabaseFactory::class,
    RouteCollector::class => RouteCollector::class,
    MiddlewarePipelineInterface::class => MiddlewarePipeline::class,
    MiddlewareResolverInterface::class => fn(ContainerInterface $c) => new MiddlewareResolver($c),
    CookieManagerInterface::class => CookieManager::class,
    ErrorHandlerMiddleware::class => ErrorHandlerMiddlewareFactory::class,
    ResponseFactoryInterface::class => CustomResponseFactory::class,
    LoggerInterface::class => LoggerFactory::class,
];
