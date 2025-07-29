<?php

declare(strict_types=1);

return [
    'debug' => true,
    'log_file' => __DIR__ . '/../var/logs/app.log',
    'database' => [
        'driver' => $_ENV['DB_DRIVER'] ?? '',
        'host' => $_ENV['DB_HOST'] ?? '',
        'port' => $_ENV['DB_PORT'] ?? '',
        'database' => $_ENV['DB_NAME'] ?? '',
        'user' => $_ENV['DB_USER'] ?? '',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'options' => [],
    ],
    'elastic' => [
    ],
];
