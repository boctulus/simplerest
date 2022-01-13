<?php

namespace simplerest\core;

use simplerest\core\libs\DB;
use simplerest\core\libs\StdOut;
use simplerest\core\traits\ExceptionHandler;

abstract class Controller
{
    use ExceptionHandler;

    protected $callable = [];
    protected $users_table;
    protected $_title;

    protected static $_printable = true;
    
    function __construct() {
        $this->config = config();

        if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }

        $this->users_table = get_users_table();
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

    function view(string $view_path, array $vars_to_be_passed = null, $layout = 'app_layout.php'){
        global $ctrl;

        $_ctrl = explode('\\',get_class($this));
        $ctrl  = $_ctrl[count($_ctrl)-1];
        $_title = substr($ctrl,0,strlen($ctrl)-10);     
        
        if(!isset($vars_to_be_passed['title'])){
            $vars_to_be_passed['title'] = $_title;
        }

        $ctrl  = strtolower(substr($ctrl, 0, -strlen('Controller')));
        $vars_to_be_passed['ctrl'] = $ctrl; //

        view($view_path, $vars_to_be_passed, $layout);
    }
}