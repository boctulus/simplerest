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
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function checkGroup(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function buttonGroup(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['role']  = "group";

        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        if (in_array('large', $kargs)){
            $attributes['class'] .= ' btn-group-lg';
            unset($args['large']);
        } else if (in_array('small', $kargs)){
            $attributes['class'] .= ' btn-group-sm';
            unset($args['small']);
        }
        
        return static::div($content, $attributes, ...$args);
    }

    static function buttonToolbar(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['role']  = "toolbar";
        return static::div($content, $attributes, ...$args);
    }


    static function alert(string $content, bool $dismissible = false, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['role']  = "alert";
        
        if ($dismissible || in_array('dismissible', $attributes)){
            $attributes['class'] .= " alert-dismissible fade show";
            $content .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        }

        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        foreach ($kargs as $k){
            if (in_array($k, static::$colors)){
                $attributes['class'] .= " alert-$k"; 
                break;
            }           
        }
            
        return static::div($content, $attributes, ...$args);
    }

    static function badge(string $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
    
        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        foreach ($kargs as $k){
            if (in_array($k, static::$colors)){
                $attributes['class'] .= " bg-$k"; 
                break;
            }           
        }
            
        return static::div($content, $attributes, ...$args);
    }

    static function breadcrumb(Array $content, Array $attributes = [], ...$args){
        $attributes['aria-label'] = "breadcrumb";   
    
        $lis = [];
        $n = count($content);

        $e = $content[0];
        for ($i=0; $i<$n; $i++){
            if (!isset($e['anchor'])){
                throw new \Exception("[ breadcrumb ] element without anchor / text");   
            }

            $anchor = $e['anchor'];
            $href   = $e['href'] ?? null;

            $active = ($i == $n);

            $inside = !empty($href) ? "<a href=\"$href\">$anchor</a>" : $anchor;
            $lis[]  = "<li class=\"breadcrumb-item $active\">$inside</li>";

            $e = next($content);
        }


        return static::group(static::group($lis, 'ol', ['class' => "breadcrumb"]), 'nav', $attributes, ...$args);
    }



    /*
        Floating labels

        https://getbootstrap.com/docs/5.0/forms/floating-labels/
    */
    static function formFloating(Array $content, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }
   
    static function alertLink(string $href, string $anchor, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($href, $anchor, $attributes, ...$args);
    }


}

