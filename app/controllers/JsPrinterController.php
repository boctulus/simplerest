<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class JsPrinterController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }
    
    function index()
    {
       js_file("vendors/printThis/printThis.js");
       view('js_print-this.php');                
    }

    function basic()
    {
       view('js_printer-2.php');                
    }
}

