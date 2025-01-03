<?php

namespace simplerest\controllers;

use MigrationsCommand;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\System;
use simplerest\core\traits\TimeExecutionTrait;

class AdminTasksController extends Controller
{
    function index(){
        $php = System::getPHP();
        dd($php, 'PHP PATH');

        dd("Bienvenido!");
    }

    function migrate()
    {   
        dd("Migrating ...");

        $mgr = new MigrationsCommand();
        $mgr->migrate(); // "--dir=$folder", "--to=$tenant"
    }
    
    /*
        --| max_execution_time
        300

        --| PHP version
        8.1.26
    */
    function show_system_vars(){
        dd(
            ini_get('max_execution_time'), 'max_execution_time'
        );

        dd(phpversion(), 'PHP version');
    }

    private function __debug_log($path, $kill = false)
    {
        if ($kill){
            Files::delete($path);
            die("Log truncated");
        }

        if (!is_cli()) echo '<pre>';
        echo Files::read($path) ?? '--x--';
        if (!is_cli()) echo '</pre>';
    }

    function log($kill = false){
        $path = LOGS_PATH . 'log.txt';
        $this->__debug_log($path, $kill);
    }

    function error_log($kill = false){
        $path = LOGS_PATH . 'errors.txt';
        $this->__debug_log($path, $kill);
    }
    
    function logs($kill = false){    
        dd("Plugin log:");  
        $this->log($kill);
                
        dd("Plugin error_log:");
        $this->error_log($kill);
    }

    function req($kill = false){
        $path = LOGS_PATH . 'req.txt';
        $this->__debug_log($path, $kill);
    }

    function res($kill = false){
        $path = LOGS_PATH . 'res.txt';
        $this->__debug_log($path, $kill);
    }
}

