<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class SweetController extends MyController
{
    function index()
    {
        css_file('third_party/sweetalert2/sweetalert2.min.css');
        js_file('third_party/sweetalert2/sweetalert.js');

        view('sweet.php');                       
    }
}

