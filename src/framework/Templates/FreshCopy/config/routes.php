<?php

use Boctulus\Simplerest\Core\WebRouter;

$route = WebRouter::getInstance();

WebRouter::any('health', function () {
    return ['ok' => true];
});

