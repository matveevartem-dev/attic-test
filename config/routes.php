<?php

declare(strict_types=1);

use App\Http\Action;
use HttpSoft\Basis\Application;
use HttpSoft\Router\RouteCollector;

return static function (Application $app): void {
    $app->get('home', '/', Action\HomeAction::class);

    $app->group('/posts', static function (RouteCollector $router) {
        $router->get('post.list', '', Action\Post\ListAction::class);
        $router->get('post.view', '/{id}', Action\Post\ViewAction::class)->tokens(['id' => '\d+']);
    });

    $app->group('/comments', static function (RouteCollector $router) {
        $router->get('comment.list', '', Action\Comment\ListAction::class);
        $router->get('comment.view', '/{id}', Action\Comment\ViewAction::class)->tokens(['id' => '\d+']);
    });

    $app->group('/api', static function (RouteCollector $router) {
        $router->get('api.search', '/search', Action\Search\SearchAction::class);
    });
};
