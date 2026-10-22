<?php

use Boctulus\Simplerest\Core\Libs\Config;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$connection = [
    'driver' => 'sqlite',
    'db_name' => ':memory:',
    'host' => null,
    'port' => null,
    'user' => null,
    'pass' => null,
    'charset' => null,
    'pdo_options' => null,
];

$configProperty = new ReflectionProperty(Config::class, 'data');
$configProperty->setAccessible(true);
$configProperty->setValue(null, [
    'db_connections' => [
        'test_sqlite' => $connection,
        'test_sqlite_secondary' => $connection,
    ],
    'db_connection_default' => 'test_sqlite',
    'namespace' => 'Boctulus\\Simplerest',
    'debug' => false,
    'pretty' => false,
]);
