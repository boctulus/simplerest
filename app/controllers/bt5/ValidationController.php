<?php

namespace simplerest\controllers\bt5;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class ValidationController extends MyController
{
    function index(){
        view('hello/validation', null, null, 15);
    }
}

