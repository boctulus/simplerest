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

        if (in_array('vertical', $kargs)){
            $attributes['class'] .= ' btn-group-vertical';
            unset($args['vertical']);
        } else {
            $attributes['class'] .= " btn-group";
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

    /* Cards Begin */

    static function card(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function cardBody(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function cardLink(string $href, string $anchor, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($href, $anchor, $attributes, ...$args);
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

    static function cardImageTop(string $src, Array $attributes = [], ...$args){
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

    static function navLink(string $href, string $anchor, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($href, $anchor, $attributes, ...$args);
    }

    /* Navigation End */

    /* Carousel Begin */

    static function carousel(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['data-bs-ride'] = "carousel";
        return static::div($content, $attributes, ...$args);
    }

    static function carouselInner(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function carouselItem(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
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


    /* Carousel End */

}

