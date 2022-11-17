<?php

namespace simplerest\controllers\datagrids;

use simplerest\controllers\MyController;
use simplerest\core\Request;

class TabulatorController extends MyController
{
    function __construct()
    {
        parent::__construct();
        
        css_file('vendors/tabulator/dist/css/tabulator.min.css');
        js_file('vendors/tabulator/dist/js/tabulator.min.js');
    }

    function index()
    {
        $v = Request::getInstance()
        ->getQuery('v') ?? '1';

        $v = str_pad($v, 2, '0', STR_PAD_LEFT);

        view("datagrids/tabulator/test{$v}");
    }                

}

