<?php

namespace Boctulus\Simplerest\Core\traits;

trait CommandTrait {
    public function handle($args) {
        if (empty($args)) {
            $this->help(); // Mostrar ayuda si no se proporcionan argumentos
            return;
        }

        $method = array_shift($args);

        if (__CLASS__ === 'HelpCommand'){
            $this->help($method, ...$args);
            return;
        }

        if (!method_exists($this, $method)) {
            dd("Method not found for " . __CLASS__ . "::$method");
            exit;
        }

        // Llamar al método dinámicamente con los argumentos restantes
        call_user_func_array([$this, $method], $args);
    }
    
    // Método de ayuda por defecto, puede ser sobrescrito por cada comando
    protected function help($name = null, ...$args) {
        dd("Usage: command [method] [args...]");
    }
}