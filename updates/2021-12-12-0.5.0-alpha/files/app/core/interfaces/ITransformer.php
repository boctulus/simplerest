<?php

namespace simplerest\core\interfaces;

use simplerest\core\controllers\Controller;

interface ITransformer {
    function transform(object $user, Controller $controller = NULL);
}