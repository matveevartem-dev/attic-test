<?php

declare(strict_types=1);

use HttpSoft\Basis\Application;
use HttpSoft\Basis\Middleware\BodyParamsMiddleware;
use HttpSoft\Basis\Middleware\ContentLengthMiddleware;
use HttpSoft\Cookie\CookieSendMiddleware;
use HttpSoft\ErrorHandler\ErrorHandlerMiddleware;
use HttpSoft\Router\Middleware\RouteDispatchMiddleware;
use HttpSoft\Router\Middleware\RouteMatchMiddleware;

return static function (Application $app): void {
    $app->pipe(ErrorHandlerMiddleware::class);
    $app->pipe(ContentLengthMiddleware::class);
    $app->pipe(BodyParamsMiddleware::class);
    $app->pipe(RouteMatchMiddleware::class);
    $app->pipe(CookieSendMiddleware::class);
    $app->pipe(RouteDispatchMiddleware::class);
};
