<?php

namespace Boctulus\Simplerest\Core\Libs;

class Module
{
    function __construct(){
        $this->includeConfigFile();
    }
    
    private function includeConfigFile(){
        $called_class = get_called_class();
        $path         = explode('\\', $called_class);
        $class_name   = array_pop($path);

        // Ruta basada en la clase hija
        $reflection = new \ReflectionClass($called_class);
        $dir = dirname($reflection->getFileName());

        Config::set($class_name, include $dir . '/config/config.php');
    }
}