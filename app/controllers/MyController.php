<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;

class MyController extends Controller
{
    static protected $default_templete = 'app_layout_basic.php';

    function __construct()
    {
        parent::__construct();        
    }

    function view(string $view_path, array $vars_to_be_passed = null, $layout = 'app_layout.php'){
        parent::view($view_path, $vars_to_be_passed, $layout = self::$default_templete);
    }
}

