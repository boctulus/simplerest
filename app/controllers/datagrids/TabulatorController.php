<?php

namespace simplerest\controllers\datagrids;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class TabulatorController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        css_file('vendors/tabulator/dist/css/tabulator.min.css');
		
        js_file('vendors/tabulator/dist/js/tabulator.min.js');

        view('datagrids/tabulator/test01');
    }                

}

