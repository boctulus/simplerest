<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class MaintenanceController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        $this->view('maintenance.php');                  
    }
}

