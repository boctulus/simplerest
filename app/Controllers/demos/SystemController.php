<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\System;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Controllers\Controller;

class SystemController extends Controller
{
    function test_exec(){
        chdir('d:\\');

        dd(
            System::com("make", "controller", "xyz")
        );
    }

}

