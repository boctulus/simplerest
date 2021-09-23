<?php

namespace simplerest\core;

use simplerest\libs\DB;
use simplerest\libs\Factory;
use simplerest\traits\ExceptionHandler;

abstract class Controller
{
    use ExceptionHandler;

    protected $callable = [];
    protected $users_table;
    protected $_title;
    
    function __construct() {
        $this->config = config();

        if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }

        $this->users_table = $this->config['users_table'];
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
        $_ctrl = explode('\\',get_class($this));
        $ctrl  = $_ctrl[count($_ctrl)-1];
        $_title = substr($ctrl,0,strlen($ctrl)-10);     
        
        if(!isset($vars_to_be_passed['title'])){
            $vars_to_be_passed['title'] = $_title;
        }

        view($view_path, $vars_to_be_passed, $layout);
   }


}