<?php

namespace simplerest\controllers\folder;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class TestController extends MyController
{    
    function test_argsv(){
        dd($_GET);
    }
}

