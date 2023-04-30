<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\Reflector;
use simplerest\controllers\MyController;

class ReflectorController extends MyController
{
    function test_refl()
    {
        dd(Reflector::getConstructor(\simplerest\core\libs\ApiClient::class));
    }
}

