<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\System;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class SystemController extends MyController
{
    function test_exec(){
        chdir('d:\\');

        dd(
            System::com("make", "controller", "xyz")
        );
    }

}

