<?php

namespace simplerest\core\libs;

/*
    Falta decorar con las clases de Boostrap 5
*/
class Form extends Html
{
    function render(string $enclosing_tag = null, Array $attributes = [], bool $pretty = true) : string {
        if (is_null($enclosing_tag)){
            $enclosing_tag = 'form';
        }

        return parent::render($enclosing_tag, $attributes, $pretty);
    }
}

