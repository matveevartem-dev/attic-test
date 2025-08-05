<?php

declare(strict_types=1);

use HttpSoft\Basis\Application;
use HttpSoft\ServerRequest\ServerRequestCreator;
use App\Infrastructure\Core\Container;
use App\Infrastructure\Core\RedisCache;
use Dotenv\Dotenv;

require_once __DIR__ . '/../autoload.php';

Dotenv::createImmutable(__DIR__ . '/../')->load();

$app = (new Container())->get(Application::class);

(require_once __DIR__ . '/../config/pipeline.php')($app);
(require_once __DIR__ . '/../config/routes.php')($app);

$app->run(ServerRequestCreator::create());
