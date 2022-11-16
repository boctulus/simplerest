<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use
    DataTables\Database,
    DataTables\Editor,
    DataTables\Editor\Field;

class FactoryPartsController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {   
        css_file('https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.13.1/b-2.3.2/sl-1.5.0/datatables.min.css');
		css_file(VIEWS_PATH . 'factory_parts/css/editor.dataTables.min.css');

        js_file('https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.13.1/b-2.3.2/sl-1.5.0/datatables.min.js');

        js_file(VIEWS_PATH . 'factory_parts/js/custom_dt.js');

        view('factory_parts/parts');
    }
}

