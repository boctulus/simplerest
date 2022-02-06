<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Form;

class Bt5Form extends Form
{   
    // por qué $classes no puede estar acá?

    /*
        Atributos aplican al checkbox y no al div
    */
    static function switch(string $text, bool $checked = false, Array $attributes = [], ...$args){
        return 
            
            static::div([
                static::checkbox($text, $checked, $attributes, ...$args)
            ],  
                class:'form-check form-switch'
            );
    }

    static function accordion(Array $items, bool $always_open = false, Array $attributes = [], ...$args){
        if (isset($args['id'])) {
            $attributes['id'] = $args['id'];
            unset($args['id']);
        }

        $elems = [];
        foreach ($items as $arr){
            $elems[] = 
                static::div(class:"accordion-item", content:
                    static::h2(class:"accordion-header", 
                    id:'heading-'.$arr['id'], 

                    /*
                        Content debería poder no ser un array sino un string
                    */
                    text:static::button(
                        class:"accordion-button collapsed",
                        data_bs_toggle:"collapse",
                        data_bs_target:"#{$arr['id']}",
                        aria_expanded:"false",
                        aria_controls:$arr['id'],
                        content:$arr['title'] 
                    )
                ) 
                .
                static::div(
                        id:$arr['id'],
                        class:"accordion-collapse collapse", 
                        aria_labelledby:'heading-'.$arr['id'],
                        data_bs_parent:!$always_open ? "#{$attributes['id']}" : null,
                        content:
                            static::div(
                                class:"accordion-body",
                                content:$arr['body']
                            ),
                        attributes:$attributes
                )
                )
            ;
        }
        
        return tag('div')
        ->content($elems)
        ->class("accordion accordion-flush")
        ->attributes($attributes);
    }

    static function inputGroup(mixed $content, Array $attributes = [], ...$args){     
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function checkGroup(mixed $content, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }


    static function alert(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['role']  = "alert";

        $kargs = array_keys($args);

        $attributes['class'] = $attributes['class'] ?? '';
        while (true){            
            if (in_array('primary', $kargs)){
                $attributes['class'] .= " alert-primary";
                break;
            }

            if (in_array('secondary', $kargs)){
                $attributes['class'] .= " alert-secondary";
                break;
            }

            if (in_array('success', $kargs)){
                $attributes['class'] .= " alert-success";
                break;
            }

            if (in_array('danger', $kargs)){
                $attributes['class'] .= " alert-danger";
                break;
            }

            if (in_array('warning', $kargs)){
                $attributes['class'] .= " alert-warning";
                break;
            }

            if (in_array('info', $kargs)){
                $attributes['class'] .= " alert-info";
                break;
            }

            if (in_array('light', $kargs)){
                $attributes['class'] .= " alert-light";
                break;
            }

            if (in_array('dark', $kargs)){
                $attributes['class'] .= " alert-dark";
                break;
            }


            break;
        }
        
        // ...

        return static::div($content, $attributes, ...$args);
    }


    /*
        Floating labels

        https://getbootstrap.com/docs/5.0/forms/floating-labels/
    */
    static function formFloating(Array $content, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }
   
}

