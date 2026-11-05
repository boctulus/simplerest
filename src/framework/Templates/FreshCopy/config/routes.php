<?php

declare(strict_types=1);
use Boctulus\Simplerest\Core\WebRouter;

$route = WebRouter::getInstance();

WebRouter::any('health', function () {
    return ['ok' => true];
});

