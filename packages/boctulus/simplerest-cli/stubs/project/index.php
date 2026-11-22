<?php

try {
    require_once __DIR__ . '/app.php';
} catch (\Boctulus\Simplerest\Core\Exceptions\InvalidRequestBodyException $e) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        exit(1);
    }

    http_response_code($e->getHttpStatus());
    header('Content-Type: application/json');
    echo json_encode([
        'error' => [
            'code' => 'INVALID_REQUEST_BODY',
            'message' => 'The request body is not valid for its Content-Type',
        ],
    ]);
    exit;
}

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\WebRouter;

$cfg = Config::get();

if ($cfg['web_router']) {
    include CONFIG_PATH . 'routes.php';
    WebRouter::compile();
    WebRouter::resolve();
}

if ($cfg['console_router']) {
    include CONFIG_PATH . 'cli_routes.php';
    CliRouter::compile();
    CliRouter::resolve();
}

if ($cfg['front_controller']) {
    FrontController::resolve();
}
