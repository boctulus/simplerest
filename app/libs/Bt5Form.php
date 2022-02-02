<?php

namespace simplerest\libs;

use simplerest\core\libs\Form;

class Bt5Form extends Form
{   
    // por qué $classes no puede estar acá?

    function __construct() { }

    function switch(string $name, string $text, bool $checked = false, Array $attributes = []){
        return 
            
            $this->div(function($html) use ($name, $text, $checked, $attributes){
                $html->checkbox($name, $text, $checked, $attributes);
            },  [
                'class' => 'form-check form-switch'
            ]);
    }

    function inputGroup(callable $closure, Array $attributes = []){     
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->div($closure, $attributes);
    }

    function checkGroup(callable $closure, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->div($closure, $attributes);
    }
   
}

