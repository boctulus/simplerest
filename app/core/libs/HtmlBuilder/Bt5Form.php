<?php

namespace simplerest\core\libs\HtmlBuilder;

use simplerest\core\libs\HtmlBuilder\Form;
use simplerest\core\libs\Strings;

class Bt5Form extends Form
{
    static function form(mixed $content, Array $attributes = [], ...$args){  
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }
    
    /* Stacked checkbox & radios */

    static function formCheck(mixed $content, Array $attributes = [], ...$args){     
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function formCheckLabel(string $for, string $text = '', Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass('label') : static::getClass('label');
        return static::label($for, $text, $attributes, ...$args);
    }

    /* Nav    */

    static function nav(mixed $content, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if (isset($args['vertical']) || (isset($attributes['vertical']) && $attributes['vertical'] !== false)){
            static::addClass('flex-column', $attributes['class']);
        } 

        if (isset($args['justifyCenter']) || (isset($attributes['justifyCenter']) && $attributes['justifyCenter'] !== false)){
            static::addClass('justify-content-center', $attributes['class']);
        } else if (isset($args['justifyRight'])){
            static::addClass('justify-content-end', $attributes['class']);
        }

        // navs

        $tabs = false;
        if (isset($args['tabs']) || (isset($attributes['tabs']) && $attributes['tabs'] !== false)){
            $tabs = true;
            static::addClass('nav-tabs', $attributes['class']);
        } 

        // pills  

        $pills = false;
        if (isset($args['pills']) || (isset($attributes['pills']) && $attributes['pills'] !== false)){
            $pills = true;
            static::addClass('nav-pills', $attributes['class']);
        } 

        // fill

        if (isset($args['fill']) || (isset($attributes['fill']) && $attributes['fill'] !== false)){
            static::addClass('nav-fill', $attributes['class']);
        } 

         // justify

         if (isset($args['justify']) || (isset($attributes['justify']) && $attributes['justify'] !== false)){
            static::addClass('nav-justified', $attributes['class']);
        } 

        $type  = $args['type'] ?? $attributes['type'] ?? 'nav';
        $panes = $args['panes'] ?? $attributes['panes'] ?? null;

        // Role ("tablist", etc)
        $role  = $args['role'] ?? $attributes['role'] ?? null;

        // if ($panes != null){
        //     d($panes);
        // }    

        $pane_list = [];
        if (is_array($content) && count($content) >0){
            foreach ($content as $ix => $e){
                if (is_array($e) && isset($e['anchor']))
                {
                    $anchor = static::shift('anchor', $e);
                    $href   = static::shift('href'  , $e, '#');

                    $active = ($ix == 0);

                    $content[$ix] = tag('navLink')->anchor($anchor)->href($href);
                    
                    if ($active){
                        $content[$ix] = ($content[$ix])->active();
                        $content[$ix]->attributes(['aria-selected' => "true"]);
                    } else {
                        $content[$ix]->attributes(['aria-selected' => "false"]);
                    }

                    $content[$ix]->attributes($e);

                    if ($role == 'tablist'){
                        if ($tabs){
                            $content[$ix]->attributes(['data-bs-toggle' => "tab"]);
                        } elseif ($pills){
                            $content[$ix]->attributes(['data-bs-toggle' => "pill"]);
                        } else {
                            throw new \Exception("Tablist require tabs or pills");
                        }    
                        
                        if (!empty($panes)){                            
                            $att = [
                                'role' => "tabpanel"
                            ];
    
                            if (isset($e['id'])){
                                $att['aria-labelledby'] = $e['id'];
                            }
    
                            $att['class'] = '';

                            if ($ix == 0){
                                $att['class'] .= 'show active ';
                            }

                            if (substr($href, 0, 1) != '#'){
                                throw new \Exception("Href '$href' should start with #");
                            }

                            $att['id'] = substr($href, 1);

                            $tb = static::tabPane($panes[$ix], $att);    
                            $pane_list[] = $tb;
                        }
                    }
                }
            }
        }

        $append = '';
        if (!empty($pane_list)){
            $append = static::tabContent($pane_list);
        }

        return static::group($content, $type, $attributes, ...$args) . $append;
    }
    
    static function navItem(string $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::li($content, $attributes, ...$args);
    }

    static function navLink(string $anchor, string $href = '#', $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if (isset($args['active'])){ 
            $attributes['aria-current'] = "page";
         }

        if (isset($args['disabled'])){
           $attributes['tabindex'] = "-1";
           $attributes['aria-disabled'] = "true";
        }

        $type = $args['as'] ?? $attributes['as'] ?? 'link';

        if ($type == 'button'){
            $attributes['data-bs-target'] = $href;
            $attributes['type'] = "button";

            return static::button($anchor, $attributes, ...$args);
        }

        return static::link($anchor, $href , $attributes, ...$args);
    }  

    static function tabPane(string $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    /* Offcanvas    */

    static function offcanvas(mixed $content = null, ?string $title = null, mixed $body = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $pos    = $args['pos'] ?? $attributes['pos'] ?? 'left';
        $scroll = $args['scroll'] ?? $attributes['scroll'] ?? null;
        $backdrop = $args['backdrop'] ?? $attributes['backdrop'] ?? null;

        if ($scroll !== null){
            $attributes['data-bs-scroll']   = "true";
            unset($args['scroll']);
        }

        if ($backdrop !== null){
            $attributes['data-bs-backdrop'] = "true";
            unset($args['backdrop']);
        }
 
        $pos_classes = [
            'left'   => 'offcanvas-start',
            'right'  => 'offcanvas-end',
            'top'    => 'offcanvas-top',
            'bottom' => 'offcanvas-bottom'
        ];

        if (!array_key_exists($pos, $pos_classes)){
            throw new \Exception("Unknown positioning class '$pos'");
        }

        static::addClass($pos_classes[$pos], $attributes['class']);
        unset($args['pos']);

        $attributes['tabindex'] = "-1"; 

        if (empty($content)){
            $body  = $body  ?? $args['body']  ?? $attributes['body']  ?? null;
            $title = $title ?? $args['title'] ?? $attributes['title'] ?? null;

            if ($title === null){
                throw new \Exception("Title is required");
            }

            if ($body === null){
                throw new \Exception("Body is required");
            }

            if (is_array($body)){
                $body = implode('', $body);
            }

            $header = tag('offcanvasHeader')->content([
                tag('offcanvasTitle')->text($title),
                tag('offcanvasCloseButton')
            ]);

            $content = $header . tag('offcanvasBody')->content($body);
        }

        return static::div($content, $attributes, ...$args);
    }

    static function offcanvasHeader(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function offcanvasBody(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function offcanvasTitle(string $text, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::h5($text, $attributes, ...$args);
    }

    static function offcanvasLink(string $anchor, string $href = '#', ?string $as = null,  $attributes = [], ...$args){
        $attributes['data-bs-toggle'] = "offcanvas";

        $type = $as ?? $args['as'] ?? $attributes['as'] ?? 'link';

        if ($type == 'button'){
            $attributes['data-bs-target'] = $href;
            $attributes['role'] = "button";
            
            return static::button($anchor, $attributes, ...$args);
        }

        return static::link($anchor, $href , $attributes, ...$args);
    }  

    static function offcanvasOpenButton(string $anchor, string $href = '#', $attributes = [], ...$args){
        return static::offcanvasLink($anchor, $href, 'button', $attributes, ...$args);
    }
    
    static function offcanvasCloseButton(mixed $content = null, Array $attributes = [], ...$args){  
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);      
        
        $attributes['data-bs-dismiss'] = "offcanvas";
        $attributes['aria-label']      = "Close";

        return static::basicButton($content, $attributes, ...$args);
    }


    /* Nav    */

    static function container(mixed $content, Array $attributes = [], ...$args){
        if (isset($args['fluid']) || (isset($attributes['fluid']) && $attributes['fluid'] !== false)){
            $attributes['class'] = "container-fluid";
            unset($args['fluid']);
        } else {
            $attributes['class'] = "container";
        }

        return static::div($content, $attributes, ...$args);
    }


    /* Navbar   */

    static function navbar(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        
        if (isset($args['expand']) || (isset($attributes['expand']) && $attributes['expand'] == false)){            
            static::addClass("navbar-expand-lg", $attributes['class']);
        }

        /*
            Ver bien en la sección de temas porque se puede customizar mucho más

            https://getbootstrap.com/docs/5.0/components/navbar/#color-schemes
        */

        $color = 'light';
        $bg    = 'light';

        if (isset($args['dark'])){
            $color = 'dark';
            $bg    = 'dark';
        }

        static::addClass("navbar-$color bg-$bg", $attributes['class']);

        return static::nav($content, $attributes, ...$args);
    }

    static function navbarBrand(string $anchor, ?string $href = null, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if ($href !== null){
            return static::link($anchor, $href, $attributes, ...$args);
        } else {
            static::addClass('mb-0 h1', $attributes['class']);
            return static::span($anchor, $attributes, ...$args);
        }
    }

    /*
        El método es casi idéntico a collapseButton
    */
    static function navbarToggler(mixed $content = null, ?string $target = null, Array $attributes = [], ...$args){  
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);      

        $content = $content ?? '<span class="navbar-toggler-icon"></span>';

        // pensar si se hace que todos los href sean target o a la inversa pero estandarizar!
        $target = $args['target'] ?? $attributes['target'] ?? $target ?? null;

        if (!empty($target)){
            $attributes['data-bs-target'] = ($target[0] != '#' && $target[0] != '.' ? "#$target" : $target);
        }

        $attributes['aria-controls'] = "navbarNav";
        $attributes['aria-expanded'] = "false";
        $attributes['aria-label']    = "Toggle navigation";

        $attributes['data-bs-toggle'] = "collapse";

        return static::basicButton($content, $attributes, ...$args);
    }

    static function navbarCollapse(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function navbarNav(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        
        if (is_array($content) && count($content) >0){
            foreach ($content as $ix => $e){
                if (is_array($e) && isset($e['anchor']))
                {
                    $anchor = static::shift('anchor', $e);
                    $href   = static::shift('href'  , $e, '#');

                    $content[$ix] = tag('navLink')->anchor($anchor)->href($href);
                    
                    // active?
                    if ($ix == 0){
                        $content[$ix] = ($content[$ix])->active();
                        $content[$ix]->attributes(['aria-selected' => "true"]);
                        $content[$ix]->attributes(['aria-current'  => "page"]);
                    } else {
                        $content[$ix]->attributes(['aria-selected' => "false"]);
                    }

                    $content[$ix]->attributes($e);
                }
            }
        }
        
        return static::div($content, $attributes, ...$args);
    }

    /* Paginator   */

    static function paginator(Array $content, Array $attributes = [], ...$args){
        $attributes['aria-label'] = 'Page naviation';

        $prev = '';
        $next = '';

        $ul_att = [
            'class' => 'pagination'
        ];

        if (array_key_exists('large', $args) || in_array('large', $attributes)){
            $ul_att['class'] .= ' pagination-lg';
            unset($args['large']);
        }

        if (array_key_exists('small', $args) || in_array('large', $attributes)){
            $ul_att['class'] .= ' pagination-sm';
            unset($args['small']);
        }

        $active_page = null;

        $withButton = function(Array $e, $active_page = null, $active = null){
            if (!isset($e['href'])){
                throw new \Exception("href is required");
            }

            if (!isset($e['anchor'])){
                throw new \Exception("anchor is required");
            }

            $href   = static::shift('href', $e);
            $anchor = static::shift('anchor', $e);
            
            $att_li = [
                'class' => 'page-item'
            ];

            if ($active_page === 0){
                $disabled = true;
            } else {
                $disabled = static::shift('disabled', $e, false);
            }    
            
            if ($disabled){
                $att_li['class'] .= ' disabled';
            }            
            
            if ($active){
                $att_li['class'] .= 'active';
                $inner = "<span class=\"page-link\">$anchor</span";
            } else {
                $inner = static::link(anchor:$anchor, href:$href, attributes:['class' => 'page-link']);
            }

            return static::li($inner, $att_li, ...$e);     
        };

        if(isset($args['withNext'])){
            if(isset($args['withNext'])){
                $next = $withButton($args['withNext'], $active_page, null);        
            }      
        }

        $options = [];
        if (array_key_exists('options', $args)){
            $options = $args['options'];
            unset($args['options']);
        }

        if (count($content) >0){
            $inc = false;
            if (array_key_first($content) === 0){
                $inc = true;
            }

            foreach ($content as $ix => $e){
                if (!isset($e['href'])){
                    throw new \Exception("href is required");
                }

                $href   = static::shift('href', $e);                
                $anchor = static::shift('anchor', $e);

                $att_li = [
                    'class' => 'page-item'
                ];

                if (empty($anchor)){
                    $anchor = $inc ? ($ix +1) : $ix;
                }

                $active = static::shift('active', $e);

                if ($active === true){
                    $active_page = $ix;
                    
                    $att_li['class'] .= ' active';
                    $inner = "<span class=\"page-link\">$anchor</span";
                } else {

                    $inner = static::link(anchor:$anchor, href:$href, attributes:['class' => 'page-link']);
                }
 
                $content[$ix] = static::li($inner, $att_li, ...$e);            
            }
        }


        if(isset($args['withPrev'])){
            $prev = $withButton($args['withPrev'], $active_page, null);        
        }
        
        $content = array_merge([$prev], $content, [$next]);

        $content = static::ul($content, $ul_att, ...$options);

        return static::group($content, 'nav', $attributes, ...$args);
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
        ->class(($args['class'] ?? '') . " accordion")
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
                
        $color   = $attributes['color'] ?? $args['color'] ?? null;
        $outline = $attributes['outline'] ?? $args['outline'] ?? false;

        if ($color !== null){
            if (!in_array($color, static::$colors)){
                throw new \InvalidArgumentException("Invalid color for '$color'");
            }

            static::addColor("alert-$color", $attributes['class'], $outline);
            unset($args['color']);
        }

        $kargs = array_merge(array_keys($args), array_keys($attributes));
 
        // Proceso colores provinientes en cualquier key => mucho más in-eficiente

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

        $target = $args['target'] ?? $attributes['target'] ?? $target ?? null;

        if (!empty($target)){
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

    static function dropdownButton(mixed $content, Array $attributes = [], ...$args){  
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);      

        if (isset($args['split']) || (isset($attributes['split']) && $attributes['split'] !== false)){
            static::addClass('dropdown-toggle-split', $attributes['class']);
            unset($args['split']);
        }

        if (isset($args['pill']) || (isset($attributes['pill']) && $attributes['pill'] !== false)){
            static::addClass('btn', $attributes['class']);
            unset($args['pill']);
        }

        $attributes['data-bs-toggle'] = "dropdown";
        return static::basicButton($content, $attributes, ...$args);
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

    static function tabContent(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    /* Dropdown End */

    

    /* Cards Begin */

    static function card(mixed $content = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        
                
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

        if (empty($content)){       
            $content = [
                    $header ?? '',
                    $body ?? '',
                    $footer ?? ''
            ];
        } else {
            if (is_array($content)){
                $content = implode('',$content);
            }    

            $content = [
                $header ?? '',
                $content,
                $body ?? '',
                $footer ?? ''
            ];
        }     
        
        return static::div($content, $attributes, ...$args);
    }

    static function cardBody(mixed $content = '', Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::div($content, $attributes, ...$args);
    }

    static function cardLink(string $anchor, string $href, $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::link($anchor, $href , $attributes, ...$args);
    }

    static function cardTitle(string $text = '', Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $color = $args['bg'] ?? $attributes['bg'] ?? null;

        $bg_color = '';
        if ($color !== null){
            if (Strings::startsWith('bg-', $color)){
                $color = substr($color, 3);
            }

            if(in_array("$color", static::$bg_colors)){
                $bg_color = " bg-{$color}";
            }    
        } 

        if (array_key_exists('placeholder', $args) || in_array('placeholder', $attributes)){
            static::addClass('placeholder-glow', $attributes['class']);

            $text = "<span class=\"placeholder col-6{$bg_color}\"></span>";

            unset($args['placeholder']);
        }

        return static::h5($text, $attributes, ...$args);
    }

    static function cardSubtitle(string $text = '', Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $color = $args['bg'] ?? $attributes['bg'] ?? null;

        $bg_color = '';
        if ($color !== null){
            if (Strings::startsWith('bg-', $color)){
                $color = substr($color, 3);
            }

            if(in_array("$color", static::$bg_colors)){
                $bg_color = " bg-{$color}";
            }    
        } 
        
        if (array_key_exists('placeholder', $args) || in_array('placeholder', $attributes)){
            static::addClass('placeholder-glow', $attributes['class']);

            $text = "<span class=\"placeholder col-9{$bg_color}\"></span>";

            unset($args['placeholder']);
        }

        return static::h6($text, $attributes, ...$args);
    }

    static function cardText(string $text = '', $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $color = $args['bg'] ?? $attributes['bg'] ?? null;

        $bg_color = '';
        if ($color !== null){
            if (Strings::startsWith('bg-', $color)){
                $color = substr($color, 3);
            }

            if(in_array("$color", static::$bg_colors)){
                $bg_color = " bg-{$color}";
            }    
        } 
        
        if (array_key_exists('placeholder', $args) || in_array('placeholder', $attributes)){
            static::addClass('placeholder-glow', $attributes['class']);

            // podría depender de la longitud del string, tamaño de la card, etc.
            $text = "
                <span class=\"placeholder col-7{$bg_color}\"></span>
                <span class=\"placeholder col-4{$bg_color}\"></span>
                <span class=\"placeholder col-4{$bg_color}\"></span>
                <span class=\"placeholder col-6{$bg_color}\"></span>
                <span class=\"placeholder col-8{$bg_color}\"></span>
            ";

            unset($args['placeholder']);
        }

        return static::p($text, $attributes, ...$args);
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

    
    /* Popover   */

    static function popover(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. 'btn btn-primary' : 'btn btn-primary';
        
        static::addClass('popovers', $attributes['class']);
        
        $attributes['data-bs-toggle'] = "popover";

        if (isset($args['title'])){
            $attributes['title'] = $args['title'];
            unset($args['title']);
        }

        if (!isset($attributes['title'])){
            throw new \Exception("Title is required");
        }

        $body  = $args['body']  ?? $attributes['body'] ?? null;

        if ($body === null){
            throw new \Exception("Body is required");
        }

        unset($args['body']);

        $attributes['data-bs-content'] = $body;

        $pos = $args['pos'] ?? $attributes['pos'] ?? 'top';

        if (!in_array($pos, ['top', 'bottom', 'left', 'right'])){
            throw new \Exception("Unknown positioning class '$pos'");
        }

        $attributes['data-bs-placement'] = $pos;
        unset($args['pos']);

        $dismissible =  ($args['dismissible'] ?? $attributes['dismissible'] ?? false);
        unset($args['dismissible']);

        if ($dismissible !== false){
            $attributes['data-bs-trigger'] = "focus";
        }        

        $type = $args['as'] ?? $attributes['as'] ?? 'link';

        if ($type == 'button'){
            $attributes['type'] = "button";
            return static::button($content, $attributes, ...$args);
        } else {
            $attributes['tabindex'] = "0";
            $attributes['role'] = "button"; 
        }

        return static::link($content, null, $attributes, ...$args);
    }



    /*
        It should be contained in a blockquote
    */
    static function blockquoteFooter(mixed $content, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::footer($content, $attributes, ...$args);
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

    static function modalDialog(mixed $content, Array $attributes = [], bool $scrollable = false, bool $centered = false, ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        if ($scrollable || array_key_exists('scrollable', $args) || in_array('scrollable', $attributes)){
            static::addClass("modal-dialog-scrollable", $attributes['class']);
            unset($args['scrollable']);
        }

        if ($centered || array_key_exists('centered', $args) || in_array('centered', $attributes)){
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

