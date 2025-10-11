<?php

use Boctulus\Simplerest\Core\WebRouter as Router;
use Boctulus\DummyApi\Controllers\ApiController;

Router::group('dummy-api', function(){
    Router::get('/test', [ApiController::class, 'getTest']);
    Router::post('/test', [ApiController::class, 'postTest']);
    Router::get('/headers', [ApiController::class, 'headersTest']);
    Router::get('/error', [ApiController::class, 'errorTest']);
});