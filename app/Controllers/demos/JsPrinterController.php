<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Controllers\WebController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

class JsPrinterController extends WebController
{
    function __construct()
    {
        parent::__construct();        
    }
    
    function index()
    {
       js_file("third_party/printThis/printThis.js");
       $this->__view('js_print-this.php');                
    }

    function basic()
    {
        $this->__view('js_printer-2.php');                
    }
}

