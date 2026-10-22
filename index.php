<?php

/*
    Framework Name: Simplerest
    Description: API Rest framework for PHP
    Author: Pablo Bozzolo
    Author Email: boctulus@gmail.com
*/

try {
    require_once __DIR__ . '/app.php';
} catch (\Boctulus\Simplerest\Core\Exceptions\InvalidRequestBodyException $e) {
    if (php_sapi_name() === 'cli') {
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

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\Simplerest\Core\FrontController;

$cfg = Config::get();

if ($cfg['web_router']){
    include __DIR__ . '/config/routes.php';
    WebRouter::compile();
    WebRouter::resolve();
}

if ($cfg['console_router']){
    include __DIR__ . '/config/cli_routes.php';
    CliRouter::compile();
    CliRouter::resolve();
}

if ($cfg['front_controller']){
    FrontController::resolve();
} 


//throw new Exception('There is no Router or FrontController enabled!');

