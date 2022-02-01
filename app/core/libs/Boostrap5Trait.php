<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

trait Boostrap5Trait
{
    /*
        Debería estar en un Boostrap5Trait.php
    */
    function switch(string $name, string $text, bool $checked = false, Array $attributes = []){
        // Parche porque se pierden las clases en tags anidados !
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


}

