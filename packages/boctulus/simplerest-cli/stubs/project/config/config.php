<?php

use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Libs\Paginator;

require_once __DIR__ . '/constants.php';

return [
    'app_url'   => Env::get('APP_URL'),
    'app_name'  => Env::get('APP_NAME'),
    'app_env'   => Env::get('APP_ENV'),
    'namespace' => 'Boctulus\Simplerest',

    // For a sub-folder in /var/www/html set it as '/folder'
    'base_url' => '',

    'web_router'       => true,
    'console_router'   => true,
    'front_controller' => true,

    'front_behaviors' => [
        'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
        'api'        => Boctulus\Simplerest\Core\Handlers\ApiHandler::class,
        'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
        'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
        'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
        'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
    ],

    // URLs start with /api/ when false
    'remove_api_slug' => false,

    'default_controller' => 'HomeController',
    'template' => 'templates/tpl_basic.php',

    'https'        => null,
    'ssl_cert'     => null,
    'DateTimeZone' => 'UTC',
    'tmp_dir'      => sys_get_temp_dir(),

    'error_handling'  => false,
    'debug'           => Env::getBool('APP_DEBUG', false),
    'error_log'       => true,
    'log_file'        => 'log.txt',
    'log_sql'         => Env::getBool('LOG_SQL', false),
    'log_errors'      => true,
    'log_stack_trace' => false,

    'users_table' => 'users',

    'include_enity_name' => false,
    'nest_sub_resources' => false,

    'paginator' => [
        'max_limit'     => 50,
        'default_limit' => 10,
        'position'      => Paginator::TOP,
        'params'        => [
            'pageSize' => 'size',
            'page'     => 'page',
        ],
        'formatter' => function ($row_count, $count, $current_page, $page_count, $page_size, $nextUrl) {
            return [
                'last_page' => $page_count,
                'paginator' => [
                    'total'       => $row_count,
                    'count'       => $count,
                    'currentPage' => $current_page,
                    'totalPages'  => $page_count,
                    'pageSize'    => $page_size,
                    'nextUrl'     => $nextUrl,
                ],
            ];
        },
    ],

    'pretty' => false,

    'restrict_by_ip'         => false,
    'restrict_by_user_agent' => false,
    'restrict_by_tenant'     => false,

    'acl_file' => SECURITY_PATH . 'acl.cache',

    'access_token' => [
        'secret_key'      => Env::get('TOKENS_ACCSS_SECRET_KEY'),
        'expiration_time' => 3600,
        'encryption'      => 'HS256',
    ],
    'refresh_token' => [
        'secret_key'      => Env::get('TOKENS_REFSH_SECRET_KEY'),
        'expiration_time' => 1209600,
        'encryption'      => 'HS256',
    ],
    'email_token' => [
        'secret_key' => Env::get('TOKENS_EMAIL_SECRET_KEY'),
        'expires_in' => 3600,
        'encryption' => 'HS256',
    ],

    'method_override' => [
        'by_url'    => true,
        'by_header' => true,
    ],

    'auto_approval_roles' => [],
    'default_role'        => null,
    'pre_activated'       => true,

    // ApiClient default sleep time between requests
    'sleep_time' => 0.15,

    'translate' => [
        'use_gettext' => false,
    ],

    // Service providers of installed packages, e.g. Vendor\Package\ServiceProvider::class
    'providers' => [],

    'cache_driver' => Boctulus\Simplerest\Core\Libs\DBCache::class,

    'var_dump_separators' => [
        'start' => '--| ',
        'end'   => '',
    ],

    'use_composer' => false,
];
