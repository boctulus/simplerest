<?php

namespace simplerest\core\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\core\traits\TimeExecutionTrait;

class WebController extends Controller
{
    function __construct() { parent::__construct(); }

    protected static $default_template = 'templates/tpl_basic.php';

    function __view(string $view_path, array $vars_to_be_passed = null, ?string $layout = null, int $expiration_time = 0){
        global $ctrl;

        $_ctrl  = explode('\\',get_class($this));
        $ctrl   = $_ctrl[count($_ctrl)-1];
        $_title = substr($ctrl,0,strlen($ctrl)-10);     
        
        if(!isset($vars_to_be_passed['title'])){
            $vars_to_be_passed['title'] = $_title;
        }

        $ctrl  = strtolower(substr($ctrl, 0, -strlen('Controller')));
        $vars_to_be_passed['ctrl'] = $ctrl; //

        if (empty($layout)){
            $layout = self::$default_template;
        }

        view($view_path, $vars_to_be_passed, $layout, $expiration_time);
    }
}

