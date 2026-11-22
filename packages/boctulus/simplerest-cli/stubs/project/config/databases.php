<?php

return [
    'db_connections' => [
        'main' => [
            'host'    => env('DB_HOST', '127.0.0.1'),
            'port'    => env('DB_PORT'),
            'driver'  => env('DB_CONNECTION'),
            'db_name' => env('DB_NAME'),
            'user'    => env('DB_USERNAME'),
            'pass'    => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],

    'db_connection_default' => 'main',

    'tentant_groups' => [],
];
