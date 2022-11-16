<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class FactoryPartsController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {   
        css_file('https://cdn.datatables.net/v/dt/jqc-1.12.4/moment-2.18.1/dt-1.13.1/b-2.3.3/date-1.2.0/sl-1.5.0/datatables.min.css');
		css_file(VIEWS_PATH . 'factory_parts/css/generator-base.css');
		css_file(VIEWS_PATH . 'factory_parts/css/editor.dataTables.min.css');

        js_file('https://cdn.datatables.net/v/dt/jqc-1.12.4/moment-2.18.1/dt-1.13.1/b-2.3.3/date-1.2.0/sl-1.5.0/datatables.min.js');
        js_file('js/dataTables.editor.min.js');

        // charset="utf-8"
        js_file('js/table.part_numbers.js', [
            'charset' => 'utf-8'
        ]);

        js_file(VIEWS_PATH . 'factory_parts/js/custom_dt.js');
        
        
        view('factory_parts/parts');
    }
}

