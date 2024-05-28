<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\System;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;

class SystemController extends Controller
{
    function test_exec(){
        chdir('d:\\');

        dd(
            System::com("make", "controller", "xyz")
        );
    }

}

