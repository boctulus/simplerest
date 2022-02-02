<?php

namespace simplerest\libs;

use simplerest\core\libs\Form;

class Bt5Form extends Form
{   
    protected $classes = [
        "text"           => "form-control",
        "number"         => "form-control",
        "password"       => "form-control",
        "email"          => "form-control",
        "file"           => "form-control",

        "date"           => "form-control",
        "time"           => "form-control",
        "datetime_local" => "form-control",
        "month"          => "form-control",
        "week"           => "form-control",
        "image"          => "form-control",
        "range"          => "form-control",
        "tel"            => "form-control",
        "url"            => "form-control",
        "area"           => "form-control",

        "select"         => "form-select",

        "checkbox"       => "form-check-input" ,
        "radio"          => "form-check-input" ,

        "label"          => "form-check-label",

        "submit"         => "btn btn-primary",
        "reset"          => "btn btn-primary",
        "inputButton"    => "btn btn-primary",

        "inputGroup"     => "input-group",
        "checkGroup"     => "form-check"
    ];

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

