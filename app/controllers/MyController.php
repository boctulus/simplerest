<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class MyController extends Controller
{
    static protected $default_templete = 'tpl_basic.php';

    function __construct()
    {
        parent::__construct();        
    }

    function view(string $view_path, array $vars_to_be_passed = null, ?string $layout = null, int $expiration_time = 0){
        parent::view($view_path, $vars_to_be_passed, self::$default_templete, $expiration_time);
    }
}

