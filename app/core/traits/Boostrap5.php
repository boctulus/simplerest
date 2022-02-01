<?php

namespace simplerest\core\traits;

trait Boostrap5
{
    function switch(string $name, string $text, bool $checked = false, Array $attributes = []){
        // Parche porque se pierden las clases en tags anidados !!!!!!!!!!
        
        if (isset($attributes['class'])){
            $attributes['class'] .= 'form-check-input';
        } else {
            $attributes['class'] = 'form-check-input';
        }
        
        return 
            
            $this->div(function($html) use ($name, $text, $checked, $attributes){
                $html->checkbox($name, $text, $checked, $attributes);
            },  [
                'class' => 'form-check form-switch'
            ]);
    }

    function inputGroup(callable $closure, Array $attributes = []){
        if (!isset($attributes['class'])){
            $attributes['class'] = 'input-group';    
        } else {
            $attributes['class'] .= ' input-group';    
        }        

        return $this->div($closure, $attributes);
    }

    function checkGroup(callable $closure, Array $attributes = []){
        if (!isset($attributes['class'])){
            $attributes['class'] = 'form-check';    
        } else {
            $attributes['class'] .= ' form-check';    
        }        

        return $this->div($closure, $attributes);
    }
}