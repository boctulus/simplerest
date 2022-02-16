<?php

namespace simplerest\core\libs;

use phpDocumentor\Reflection\Types\Null_;
use simplerest\core\libs\Form;

class Bt5Form extends Form
{   
    /* Stacked checkbox & radios */

    static function formCheck(mixed $content, Array $attributes = [], ...$args){     
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function formCheckLabel(string $for, string $text = '', Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass('label') : static::getClass('label');
        return static::label($for, $text, $attributes, ...$args);
    }

    /* List group */

    static function listGroup(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        
        if ((isset($attributes['flush']) && $attributes['flush']) || (isset($args['flush']) && $args['flush']  !== false)){
            static::addClass('list-group-flush', $attributes['class']);
        }

        if ((isset($attributes['horizontal']) && $attributes['horizontal']) || (isset($args['horizontal']) && $args['horizontal']  !== false)){
            static::addClass('list-group-horizontal', $attributes['class']);
        }

        if ((isset($attributes['numbered']) && $attributes['numbered']) || (isset($args['numbered']) && $args['numbered'] !== false)){
            static::addClass('list-group-numbered', $attributes['class']);

            return static::ol($content, $attributes, ...$args);
        }

        return static::ul($content, $attributes, ...$args);
    }

    static function listGroupItem(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if ((isset($attributes['active']) && $attributes['active']) || (isset($args['active']) && $args['active'] !== false)){
            static::addClass('active', $attributes['class']);
            $attributes['aria-current'] = "true";
        }

        // Proceso colores por si se envian usando color($color)
        
        $color = $attributes['color'] ?? $args['color'] ?? null;

        if ($color !== null){
            if (!in_array($color, static::$colors)){
                throw new \InvalidArgumentException("Invalid color for '$color'");
            }

            static::addClass("list-group-item-{$color}", $attributes['class']);
            unset($args['color']);
        }

        // Proceso colores provinientes en cualquier key => mucho más ineficiente

        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        foreach ($kargs as $k){
            if (in_array($k, static::$colors)){
                static::addClass("list-group-item-{$k}", $attributes['class']); 
                break;
            }           
        }

        if ((isset($attributes['actionable']) && $attributes['actionable']) || (isset($args['actionable']) && $args['actionable'] !== false)){
            static::addClass('list-group-item list-group-item-action', $attributes['class']);

            return static::button($text, $attributes, ...$args);
        }

        return static::li($text, $attributes, ...$args);
    }

    /*
        Nota: atributos aplican al checkbox y no al div
    */
    static function switch(string $text, bool $checked = false, Array $attributes = [], ...$args){
        return 
            
            static::div([
                static::checkbox($text, $checked, $attributes, ...$args)
            ],  
                class:'form-check form-switch'
            );
    }

    static function accordion(Array $items, ?bool $always_open = null, Array $attributes = [], ...$args){
        if (isset($args['id'])) {
            $attributes['id'] = $args['id'];
            unset($args['id']);
        }

        if ($always_open === null){
            $always_open = $attributes['always_open'] ?? false;
        }

        $elems = [];
        foreach ($items as $ix => $arr){
            $elems[] = 
                static::div(class:"accordion-item", content:
                    static::h2(class:"accordion-header", 
                    id:'heading-'.$arr['id'], 

                    /*
                        Content debería poder no ser un array sino un string
                    */
                    text:static::basicButton(
                        class:"accordion-button" . ($ix != 0 ? ' collapsed' : ''),
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
                        class:"accordion-collapse collapse" . ($ix == 0 ? ' show' : ''), 
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
        ->class("accordion")
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
            static::addClass('btn-group-lg', $attributes['class']);
            unset($args['large']);
        } else if (in_array('small', $kargs)){
            static::addClass('btn-group-sm', $attributes['class']);
            unset($args['small']);
        }

        if (in_array('vertical', $kargs)){
            static::addClass('btn-group-vertical', $attributes['class']);
            unset($args['vertical']);
        } else {
            static::addClass('btn-group', $attributes['class']);
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
            static::addClasses('alert-dismissible fade show', $attributes['class']);
            $content .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        }

        // Proceso colores por si se envian usando color($color)
                
        $color = $attributes['color'] ?? $args['color'] ?? null;

        if ($color !== null){
            if (!in_array($color, static::$colors)){
                throw new \InvalidArgumentException("Invalid color for '$color'");
            }

            static::addClass(" alert-$color", $attributes['class']);
            unset($args['color']);
        }

        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        // Proceso colores provinientes en cualquier key => mucho más ineficiente

        foreach ($kargs as $k){
            if (in_array($k, static::$colors)){
                static::addClass(" alert-$k", $attributes['class']); 
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
                static::addClass("bg-$k", $attributes['class']);
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
   
    static function alertLink(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($anchor, $href , $attributes, ...$args);
    }

    static function link(string $anchor, ?string $href = null, Array $attributes = [], ...$args){       
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        // title es requerido (sin probar)
        if (array_key_exists('tooltip', $args)){
            $attributes['data-bs-toggle'] = "tooltip";
        }

        return parent::link($anchor, $href, $attributes, ...$args);
    }


    /* Collapse Begin */

    static function collapseLink(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. 'btn btn-primary' : 'btn btn-primary';
        
        $attributes['data-bs-toggle'] = "collapse";
        $attributes['role'] = "button";    

        return static::link($anchor, $href, $attributes, ...$args);
    }

    static function collapseButton(mixed $content, Array $attributes = [], $target = null, ...$args){        
        $attributes['data-bs-toggle'] = "collapse";

        if (!is_null($target)){
            $attributes['data-bs-target'] = ($target[0] != '#' && $target[0] != '.' ? "#$target" : $target);
        }

        return static::button($content, $attributes, ...$args);
    }

    static function collapse(mixed $content, Array $attributes = [], bool $multiple = false, ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
       
        if ($multiple || isset($args['multiple'])){
            static::addClass('multi-collapse', $attributes['class']);
            unset($args['multiple']);
        }
       
        return static::div($content, $attributes, ...$args);
    }

    static function collapseBody(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    /* Collapse End   */


    /* Dropdown Begin*/

    static function dropdown(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function dropdownButton(mixed $content, Array $attributes = [], $target = null, ...$args){  
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);      

        if (isset($args['split'])){
            static::addClass('dropdown-toggle-split', $attributes['class']);
            unset($args['split']);
        }

        $attributes['data-bs-toggle'] = "dropdown";
        return static::button($content, $attributes, ...$args);
    }

    static function dropdownLink(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. 'btn btn-secondary dropdown-toggle' : 'btn btn-secondary dropdown-toggle';

        $attributes['data-bs-toggle'] = "dropdown";
        $attributes['role'] = "button";    

        return static::link($anchor, $href , $attributes, ...$args);
    }

    static function dropdownMenu(mixed $content, string $ariaLabel, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $attributes['aria-labelledby'] = $ariaLabel;

        return static::ul($content, $attributes, ...$args);
    }

    static function dropdownItem(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return '<li>' . static::link($anchor, $href , $attributes, ...$args) . '</li>';
    }

    static function dropdownDivider(){
        return '<li><hr class="dropdown-divider"></li>';
    }

    /* Dropdown End */

    

    /* Cards Begin */

    static function card(mixed $content = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        
        if (empty($content)){           
            if (array_key_exists('header', $args)){
                $header = static::cardHeader($args['header']);
                unset($args['header']);
            }

            if (array_key_exists('body', $args)){
                $body = static::cardBody($args['body']);
                unset($args['body']);
            }

            if (array_key_exists('footer', $args)){
                $footer = static::cardFooter($args['footer']);
                unset($args['footer']);
            }

            $content = [
                    $header ?? '',
                    $body ?? '',
                    $footer ?? ''
            ];
        }        
        
        return static::div($content, $attributes, ...$args);
    }

    static function cardBody(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function cardLink(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($anchor, $href , $attributes, ...$args);
    }

    static function cardText(string $text, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::p($text, $attributes, ...$args);
    }

    static function cardTitle(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::h5($text, $attributes, ...$args);
    }

    static function cardSubtitle(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::h6($text, $attributes, ...$args);
    }

    static function cardImg(string $src, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::img($src, $attributes, null, ...$args); 
    }

    static function cardImgTop(string $src, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::img($src, $attributes, null, ...$args); 
    }

    static function cardImgBottom(string $src, Array $attributes = [], ...$args){
        return static::img($src, $attributes, null, ...$args); 
    }

    static function cardImgOverlay(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function cardListGroup(mixed $content, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::ul($content, __FUNCTION__, $attributes, ...$args);
    }

    static function cardListGroupItem(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::li($text, $attributes, ...$args);
    }

    static function cardHeader(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function cardHeaderTabs(mixed $content, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::ul($content, __FUNCTION__, $attributes, ...$args);
    }

    static function cardFooter(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    // Seguir por "Card styles" 
    // https://getbootstrap.com/docs/5.0/components/card/#image-overlays


    /* Cards End */

    /*
        It should be contained in a blockquote
    */
    static function blockquoteFooter(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::footer($content, $attributes, ...$args);
    }

    /* Navigation Begin */

    static function navItem(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::li($text, $attributes, ...$args);
    }

    static function navLink(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($anchor, $href , $attributes, ...$args);
    }

    /* Navigation End */


    /* Carousel Begin */

    static function carousel(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['data-bs-ride'] = "carousel";

        if (array_key_exists('dark', $args)){
            static::addClass(static::$classes["carouselDark"], $attributes['class']);
            unset($attributes['dark']);
        }

        if (array_key_exists('fade', $args)){
            static::addClass(static::$classes["carouselFade"], $attributes['class']);
            unset($attributes['fade']);
        }

        if (array_key_exists('withIndicators', $args)){
            $indicators = tag('carouselIndicators')->content([
                tag('button')->dataBsTarget("#carouselExampleIndicators")->dataBsSlideTo("0")->aria_current("true")
                ->content()->active(),
                tag('button')->dataBsTarget("#carouselExampleIndicators")->dataBsSlideTo("1")->aria_current("true")
                ->content(),
                tag('button')->dataBsTarget("#carouselExampleIndicators")->dataBsSlideTo("2")->aria_current("true")
                ->content()
            ]);

            unset($attributes['withIndicators']);
        }

        if (array_key_exists('withControls', $args)){
            $controls = tag('carouselControlPrev')->content(
                tag('carouselControlPrevIcon')->text() .
                tag('span')->hidden()->text('Previous')
            )->dataBsTarget("#carouselExampleControls") .

            tag('carouselControlNext')->content(
                tag('carouselControlNextIcon')->text() .
                tag('span')->hidden()->text('Next')
            )->dataBsTarget("#carouselExampleControls");

            unset($attributes['withControls']);
        }

        if (is_array($content)){
            $content[0] = ($content[0])->active();
        }

        $content = [
            $indicators ?? '',
            static::carouselInner($content),
            $controls ?? ''
        ];

        return static::div($content, $attributes, ...$args);
    }

    static function carouselInner(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function carouselItem(mixed $content, Array $attributes = [], int $interval = -1, mixed $caption = null, ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if ($interval > 0){
            $attributes['data-bs-interval'] = $interval;
        }

        if (!empty($caption)){
            $content .= tag('carouselCaption')->content($caption
            )->class("d-none d-md-block");
        }

        return static::div($content, $attributes, ...$args);
    }

    static function carouselIndicators(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function carouselCaption(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function carouselControlPrev(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['data-bs-slide'] = "prev";
        return static::button($content, $attributes, ...$args);
    }

    static function carouselControlNext(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['data-bs-slide'] = "next";
        return static::button($content, $attributes, ...$args);
    }

    static function carouselControlPrevIcon(mixed $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['aria-hidden']="true";
        return static::span($text, $attributes, ...$args);
    }

    static function carouselControlNextIcon(mixed $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['aria-hidden']="true";
        return static::span($text, $attributes, ...$args);
    }

    static function carouselImg(string $src, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::img($src, $attributes, null, ...$args); 
    }

    /* Carousel End */

    static function closeButton(mixed $content = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $attributes['aria-label'] = "Close";

        if (array_key_exists('white', $args)){
            static::addClass("btn-close-white", $attributes['class']);
        }

        return static::basicButton($content, $attributes, ...$args);
    }

    /* Modal Begin */

    static function modal(mixed $content = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $attributes['tabindex'] = "-1";

        if (array_key_exists('static', $args)){
            $attributes['data-bs-backdrop'] = "static"; 
            $attributes['data-bs-keyboard'] = "false";
        }

        $options = [];
        if (array_key_exists('options', $args)){
            $options = $args['options'];
            unset($args['options']);
        }

        if (empty($content)){           
            if (array_key_exists('header', $args)){
                $header = static::modalHeader($args['header']);
                unset($args['header']);
            }

            if (array_key_exists('body', $args)){
                $body = static::modalBody($args['body']);
                unset($args['body']);
            }

            if (array_key_exists('footer', $args)){
                $footer = static::modalFooter($args['footer']);
                unset($args['footer']);
            }

            $content = tag('modalDialog')
            ->content(
                tag('modalContent')->content([
                    $header ?? '',
                    $body ?? '',
                    $footer ?? ''
                ])
            )->attributes($options);
        }

        return static::div($content, $attributes, ...$args);
    }


    static function closeModal(mixed $content = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if (empty($content)){
            $content = 'Close';
        }

        $attributes['data-bs-dismiss'] = 'modal';
        static::addClass('btn-secondary', $attributes['class']);

        return static::button($content, $attributes, ...$args);
    }

    static function openButton(mixed $content, string $target, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        
        static::addClass('btn-primary', $attributes['class']);

        $attributes['data-bs-toggle'] = "modal";
        $attributes['data-bs-target'] = ($target[0] != '#' && $target[0] != '.' ? "#$target" : $target);

        return static::button($content, $attributes, ...$args);
    }

    static function modalDialog(mixed $content, Array $attributes = [], bool $scrollable = false, bool $center = false, ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if ($scrollable || array_key_exists('scrollable', $args) || in_array('scrollable', $attributes)){
            static::addClass("modal-dialog-scrollable", $attributes['class']);
            unset($args['scrollable']);
        }

        if ($center || array_key_exists('center', $args) || in_array('center', $attributes)){
            static::addClass("modal-dialog-centered", $attributes['class']);
            unset($args['center']);
        }

        /*
            No está funcionando el cambio de size
        */

        if (array_key_exists('small', $args) || in_array('small', $attributes)){
            static::addClass('modal-sm', $attributes['class']);
            unset($args['small']);
        }

        if (array_key_exists('large', $args) || in_array('large', $attributes)){
            static::addClass('modal-lg', $attributes['class']);
            unset($args['large']);
        }

        if (array_key_exists('extraLarge', $args) || in_array('extraLarge', $attributes)){
            static::addClass('modal-xl', $attributes['class']);
            unset($args['extraLarge']);
        }

        // Full screen

        if (array_key_exists('fullscreen', $args) || in_array('fullscreen', $attributes)){
            static::addClass('modal-fullscreen', $attributes['class']);
            unset($args['fullscreen']);
        }


        return static::div($content, $attributes, ...$args);
    }

    static function modalContent(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        return static::div($content, $attributes, ...$args);
    }

    static function modalHeader(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        return static::div($content, $attributes, ...$args);
    }

    static function modalBody(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        return static::div($content, $attributes, ...$args);
    }

    static function modalFooter(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        return static::div($content, $attributes, ...$args);
    }

    static function modalTitle(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::h5($text, $attributes, ...$args);
    }
    

    /* Modal End */

}

