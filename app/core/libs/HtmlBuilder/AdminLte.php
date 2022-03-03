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
   
    /*
        Puede manejarse con Javascript

        Ej:

        $('#range_1').ionRangeSlider({
            min     : 0,
            max     : 5000,
            from    : 1000,
            to      : 4000,
            type    : 'double',
            step    : 1,
            prefix  : '$',
            prettify: false,
            hasGrid : true
        })

        $('#range_5').ionRangeSlider({
            min     : 0,
            max     : 10,
            type    : 'single',
            step    : 0.1,
            postfix : ' mm',
            prettify: false,
            hasGrid : true
        })
    */
    static function ionSlider(mixed $default = null, Array $attributes = [], ...$args){
        /*
            Incluir el CSS acá genera dos problemas muy graves:

            1) No puede ser cacheado y 

            2) Queda repetido tantas veces como se incluya el componente! 

            La solución para "producción" sería "compilar" el las vistas con lo cual los archivos css 
            de cada componente serían incluídos una sola vez para la vista correspondiente.

            En si,... include_css() debería "encolar" los archivos css para la vista corespondiente.
        */

        include_css(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/css/ion.rangeSlider.min.css');

        $att = [
        ];

        // symbol == postfix
        $postfix = $args['symbol'] ?? $attributes['symbol'] ?? $args['postfix'] ?? $attributes['postfix'] ?? null;

        if ($postfix){
            $att['data-postfix'] = $postfix;

            unset($args['postfix']);
            unset($args['symbol']);
        }

        $step = $args['step'] ?? $attributes['step'] ?? null;

        if ($step){
            $att['data-step'] = $step;

            unset($args['step']);
        }

        $from = $args['from'] ?? $attributes['from'] ?? null;

        if ($from){
            $att['data-from'] = $from;

            unset($args['from']);
        }

        $to = $args['to'] ?? $attributes['to'] ?? null;

        if ($to){
            $att['data-to'] = $to;

            unset($args['to']);
        }


        $min = $args['min'] ?? $attributes['min'] ?? null;

        if ($min){
            $att['data-min'] = $min;

            unset($args['min']);
        }

        $max = $args['max'] ?? $attributes['max'] ?? null;

        if ($max){
            $att['data-max'] = $max;

            unset($args['max']);
        }

        $type = $args['type'] ?? $attributes['type'] ?? 'single';

        if ($type != 'single' && $type != 'double'){
            throw new \InvalidArgumentException("Type should be single or double");
        }

        $att['data-type'] = $max;
        unset($args['type']);


        $id = $args['id'] ?? $attributes['id'] ?? null;
        
        if ($id){
            $att['after_tag'] = 
            js("
                $(function () {
                    $('#$id').ionRangeSlider({})
                });
            ");
        }

        $attributes = $attributes + $att;

        return static::inputText($default, $attributes, ...$args);
    }


}

