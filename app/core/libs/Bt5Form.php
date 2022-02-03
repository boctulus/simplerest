<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Form;

class Bt5Form extends Form
{   
    // por qué $classes no puede estar acá?

    function __construct() { }

    function switch(string $text, bool $checked = false, Array $attributes = [], ...$args){
        return 
            
            $this->div(function($html) use ($text, $checked, $attributes, $args){
                $html->checkbox($text, $checked, $attributes, ...$args);
            },  [
                'class' => 'form-check form-switch'
            ]);
    }

    function inputGroup(callable $closure, Array $attributes = [], ...$args){     
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->div($closure, $attributes, ...$args);
    }

    function checkGroup(callable $closure, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->div($closure, $attributes, ...$args);
    }
   
}

