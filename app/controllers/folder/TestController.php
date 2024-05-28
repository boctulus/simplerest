<?php

namespace simplerest\controllers\folder;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class TestController extends Controller
{    
    function test_argsv(){
        dd($_GET);
    }
}

