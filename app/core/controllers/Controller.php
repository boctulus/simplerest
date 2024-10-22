<?php

namespace simplerest\core\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\StdOut;
use simplerest\core\traits\ExceptionHandler;

abstract class Controller
{
    use ExceptionHandler;

    protected $callable = [];
    protected $_title;
    protected $config;
    protected static $default_template = 'templates/tpl_basic.php';
    
    function __construct() {
        $this->config = config();

        if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }

        if (!is_cli()){
            http_response_code(200);
        }
    }

    protected function getConnection() {
        return DB::getConnection();
    }

    function getCallable(){
        return $this->callable;
    }

    function addCallable(string $method){
        $this->callable = array_unique(array_merge($this->callable, [$method]));
    }

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