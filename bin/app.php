#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

use App\Infrastructure\Console\Command\MigrateCommand;
use App\Infrastructure\Console\Command\ImportCommand;
use App\Infrastructure\Console\Command\TruncateCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new MigrateCommand());
$app->add(new ImportCommand());
$app->add(new TruncateCommand());

$app->run();