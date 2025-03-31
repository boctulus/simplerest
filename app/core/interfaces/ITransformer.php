<?php

namespace Boctulus\Simplerest\Core\Interfaces;

use Boctulus\Simplerest\Core\Controllers\Controller;

interface ITransformer {
    function transform(object $user, Controller $controller = NULL);
}