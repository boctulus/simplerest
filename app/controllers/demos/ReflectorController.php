<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\Reflector;
use simplerest\core\controllers\Controller;

class ReflectorController extends Controller
{
    function test_refl()
    {
        dd(Reflector::getConstructor(\simplerest\core\libs\ApiClient::class));
    }
}

