<?php

namespace simplerest\core\libs\HtmlBuilder;

use simplerest\core\libs\Strings;

/*
    Re-implementar:

    Alerts -- done
    Cards
    inputColor
    Select
    Accordion (un reemplazo)
    switch 
    checkbox

    En BTS cambian los data-* por data-bs-* 

    Ej:

    data-toggle por data-bs-toggle
*/

class AdminLte extends Bt5Form
{
    static function alert(string $content, bool $dismissible = false, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['role']  = "alert";

        $title = $args['title'] ?? $attributes['title'] ?? '';
        
        $close_btn = '';
        if ($dismissible || in_array('dismissible', $attributes)){
            static::addClasses('alert-dismissible fade show', $attributes['class']);
            $close_btn = '<button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">×</button>';
        }

        // Proceso colores por si se envian usando color($color)
                
        $color   = $attributes['color'] ?? $args['color'] ?? null;
 
        if ($color !== null){
            if (!in_array($color, static::$colors)){
                throw new \InvalidArgumentException("Invalid color for '$color'");
            }

            static::addColor("alert-$color", $attributes['class']);
            unset($args['color']);
        }

        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        // Proceso colores provinientes en cualquier key => mucho más in-eficiente

        foreach ($kargs as $k){
            if (in_array($k, static::$colors)){
                static::addClass(" alert-$k", $attributes['class']); 
                $color = $k;
                break;
            }           
        }

        $icons = [
            'danger'  => 'fa-ban',
            'info'    => 'fa-info',
            'warning' => 'fa-exclamation-triangle',
            'success' => 'fa-check'
        ];

        $_title = '';
        if ($title != null && isset($icons[$color])){
            $icon   = $icons[$color];
            $_title = "<h5><i class=\"icon fas $icon\"></i> $title </h5>";
        }
            
        $content = $close_btn . $_title .$content;

        return static::div($content, $attributes, ...$args);
    }

    static function appButton(string $content, string $href, $attributes = [], ...$args){
        $_ = "btn btn-app";
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $_ : $_;

        $icon = $args['icon'] ?? $attributes['icon'] ?? null;

        if (!$icon){
            throw new \Exception("icon is required");
        }

        $icon    = Strings::replaceFirst('fa-', '', $icon);
        unset($args['icon']);
    
        $qty = $args['badgeQty'] ?? $attributes['badgeQty'] ?? null;

        $badge = '';
        if ($qty !== null){
            $badge_color = $args['badgeColor'] ?? $attributes['badgeColor'] ?? 'danger';
        
            if ($badge_color !== null){          
                unset($args['badgeColor']);
            } 

            $badge = tag('span')->class('badge')->text($qty)->bg($badge_color);
            unset($args['qty']);
        }

        $anchor = $badge . "<i class=\"fas fa-{$icon}\"></i> $content</a>";

        return static::link($anchor, $href , $attributes, ...$args);
    }
   

}
