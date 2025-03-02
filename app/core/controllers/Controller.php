<?php

namespace simplerest\core\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Config;
use simplerest\core\traits\ExceptionHandler;

abstract class Controller
{
    use ExceptionHandler;

    protected $callable = [];
    protected $_title;
    protected $config;
    protected $output_format = 'auto'; // Valores posibles: 'auto', 'json', 'pretty_json', 'dd'
    
    function __construct() {
        $this->config = Config::get();

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

    public function setOutputFormat(string $format)
    {
        $valid_formats = ['auto', 'test', 'json', 'pretty_json', 'dd'];
        if (!in_array($format, $valid_formats)) {
            throw new \InvalidArgumentException("Invalid output format: $format");
        }
        $this->output_format = $format;
    }
    
    public function getOutputFormat(): string 
    {
        return $this->output_format;
    }
}