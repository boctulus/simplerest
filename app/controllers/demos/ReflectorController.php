<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\Reflector;
use Boctulus\Simplerest\Core\Controllers\Controller;

class ReflectorController extends Controller
{
    function test_refl()
    {
        dd(Reflector::getConstructor(\Boctulus\Simplerest\Core\Libs\ApiClient::class));
    }
}

