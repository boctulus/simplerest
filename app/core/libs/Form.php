<?php

namespace simplerest\core\libs;

/*
    Falta decorar con las clases de Boostrap 5
*/
class Form extends Html
{
    function render(?string $enclosing_tag = 'form', Array $attributes = [], bool $pretty = true) : string {
        return parent::render($enclosing_tag, $attributes, $pretty);
    }
}

