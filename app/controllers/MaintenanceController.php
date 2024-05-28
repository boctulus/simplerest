<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class MaintenanceController extends Controller
{
    function index()
    {
        $this->__view('maintenance.php');                  
    }
}

