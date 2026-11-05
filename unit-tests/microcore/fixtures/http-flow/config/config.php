<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Handlers\ApiHandler;
use Boctulus\Simplerest\Core\Handlers\AuthHandler;
use Boctulus\Simplerest\Core\Handlers\ErrorHandler;
use Boctulus\Simplerest\Core\Handlers\MiddlewareHandler;
use Boctulus\Simplerest\Core\Handlers\OutputHandler;
use Boctulus\Simplerest\Core\Handlers\RequestHandler;

return [
    'app_url'   => 'http://127.0.0.1',
    'app_name'  => 'http-flow-fixture',
    'app_env'   => 'testing',
    'namespace' => 'Boctulus\\Simplerest',

    'base_url' => '',
    'https'    => null,
    'DateTimeZone' => 'UTC',

    'web_router'       => true,
    'console_router'   => false,
    'front_controller' => true,

    'front_behaviors' => [
        'request'    => RequestHandler::class,
        'api'        => ApiHandler::class,
        'auth'       => AuthHandler::class,
        'output'     => OutputHandler::class,
        'middleware' => MiddlewareHandler::class,
        'error'      => ErrorHandler::class,
    ],

    'remove_api_slug'    => false,
    'default_controller' => 'HomeController',
    'template'           => 'templates/tpl.php',

    'error_handling' => false,
    'debug'          => false,
    'error_log'      => false,
    'log_file'       => 'log.txt',
    'log_sql'        => false,
    'log_errors'     => true,
    'log_stack_trace'=> false,

    'users_table' => 'users',
    'pretty'      => false,

    'paginator' => [
        'max_limit'     => 50,
        'default_limit' => 10,
        'position'      => 1,
        'params'        => ['pageSize' => 'pageSize', 'page' => 'page'],
    ],

    'method_override' => ['by_url' => true, 'by_header' => true],
];
