<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

class SweetController extends Controller
{
    function index()
    {
        css_file('third_party/sweetalert2/sweetalert2.min.css');
        js_file('third_party/sweetalert2/sweetalert.js');

        view('sweet.php');                       
    }
}

