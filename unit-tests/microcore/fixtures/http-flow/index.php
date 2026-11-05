<?php

declare(strict_types=1);

/*
 * Full microcore HTTP flow fixture.
 *
 * Boots only src/framework: no vendor/, no packages/, no application
 * bootstrap. Used by HttpFlowSmokeTest to exercise index -> WebRouter miss ->
 * FrontController -> controller -> OutputHandler -> Response::flush().
 */

$fixtureRoot = __DIR__ . DIRECTORY_SEPARATOR;

define('ROOT_PATH', $fixtureRoot);
define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', APP_PATH . 'Controllers' . DIRECTORY_SEPARATOR);
define('STORAGE_PATH', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-http-flow' . DIRECTORY_SEPARATOR);
define('LOGS_PATH', STORAGE_PATH . 'logs' . DIRECTORY_SEPARATOR);
define('CACHE_PATH', STORAGE_PATH . 'cache' . DIRECTORY_SEPARATOR);

foreach ([STORAGE_PATH, LOGS_PATH, CACHE_PATH] as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

$core = dirname(__DIR__, 4) . '/src/framework/';

spl_autoload_register(static function (string $class) use ($core): void {
    $prefixes = [
        'Boctulus\\Simplerest\\Core\\' => $core,
        'Boctulus\\Simplerest\\Controllers\\' => __DIR__ . '/app/Controllers/',
    ];

    foreach ($prefixes as $prefix => $base) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $file = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }
});

foreach ([
    'polyfill.php',
    'env.php',
    'config.php',
    'factories.php',
    'http.php',
    'system.php',
    'url.php',
    'db.php',
    'debug.php',
    'loggers.php',
    'view.php',
] as $helper) {
    require_once $core . 'Helpers/' . $helper;
}

use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\WebRouter;

require CONFIG_PATH . 'routes.php';

WebRouter::compile();
WebRouter::resolve();

FrontController::resolve();
