<?php

namespace simplerest\controllers\demos;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class JsPrinterController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }
    
    function index()
    {
       js_file("third_party/printThis/printThis.js");
       view('js_print-this.php');                
    }

    function basic()
    {
       view('js_printer-2.php');                
    }
}

