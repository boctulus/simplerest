<?php

namespace simplerest\controllers\datagrids;

use simplerest\controllers\MyController;
use simplerest\core\Request;

class TabulatorController extends MyController
{
    function __construct()
    {
        parent::__construct();
        
        css_file('third_party/tabulator/dist/css/tabulator.min.css');
        //css_file('third_party/tabulator/dist/css/tabulator_bootstrap5.min.css');

        js_file('third_party/tabulator/dist/js/tabulator.min.js');
    }

    function index()
    {
        /*
            /?v={version}
        */
        $v = Request::getInstance()
        ->getQuery('v') ?? '1';

        $v = str_pad($v, 2, '0', STR_PAD_LEFT);

        view("datagrids/tabulator/test{$v}");
    }                

}

