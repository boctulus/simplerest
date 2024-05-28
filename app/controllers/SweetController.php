<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class SweetController extends Controller
{
    function index()
    {
        css_file('third_party/sweetalert2/sweetalert2.min.css');
        js_file('third_party/sweetalert2/sweetalert.js');

        view('sweet.php');                       
    }
}

