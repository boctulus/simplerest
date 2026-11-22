<?php

use Boctulus\Simplerest\Core\WebRouter;

$route = WebRouter::getInstance();

WebRouter::any('system/health', function () {
    return ['ok' => true];
});
