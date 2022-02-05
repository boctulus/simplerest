<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Tag;

class Html
{
    static protected $pretty     = false;
    static protected $id_eq_name = false;
    static protected $classes = [
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
        "dataList"       => "form-control",

        "range"          => "form-range",

        "select"         => "form-select",

        "checkbox"       => "form-check-input" ,
        "radio"          => "form-check-input" ,

        "label"          => "form-label",

        "submit"         => "btn btn-primary",
        "reset"          => "btn btn-primary",
        "inputButton"    => "btn btn-primary",

        "inputGroup"     => "input-group",
        "checkGroup"     => "form-check",
        "formFloating"   => "form-floating",

        "color"          => "form-control form-control-color"
    ];
    
    static protected $macros = [];

    static function pretty(bool $state = true){
        static::$pretty = $state;
    }

    /*
        Copy name attribute into id one
    */
    static function setIdAsName(bool $state = true){
        static::$id_eq_name = $state;
    }

    static protected function attributes(Array $atts) : string{
        $_att = [];
        foreach ($atts as $att => $val){
            $_att[] = "$att=\"$val\"";
        }

        return implode(' ', $_att);
    }

    static public function getClass(string $tag) : string{
        return static::$classes[$tag] ?? '';
    }

    static protected function renderTag(string $type, ?string $value = '', Array $attributes = [], Array $plain_attr = [], ...$args) : string
    {
        if (isset($attributes['class']) && isset($args['class'])){
            $attributes['class'] .=  ' ' . $args['class'];
            unset($args['class']);
        }

        $attributes = array_merge($attributes, $args);

        $name = $attributes['name'] ?? '';

        if (!empty($name) && static::$id_eq_name){
            $attributes['id'] = $name;
        }

        $att_str = static::attributes($attributes);
        $p_atr   = implode(' ', $plain_attr);

        $props = trim("$att_str $p_atr");
        $props = !empty($props) ? ' '.$props : $props;

        // en principio asumo que abre y cierra
        $ret = "<$type" . $props . ">$value</$type>";

        return static::$pretty ? static::beautifier($ret) : $ret;
    }

    static function tag(string $type, ?string $value = '', Array $attributes = [], Array $plain_attr = [], ...$args){
        return static::renderTag($type, $value, $attributes, $plain_attr, ...$args);
    }

    static function group(Array $content, string $tag = 'div', Array $attributes = [], ...$args){
        $content_str = implode(' ', $content);
        return static::tag($tag, $content_str, $attributes, [], ...$args);
    }

    static function link_to(string $href, string $anchor, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);

        if (Strings::startsWith('www.', $href)){
            $href = "http://$href";
        }

        $attributes['href'] = $href;

        return static::renderTag('a', $anchor, $attributes);
    }

    static function input(string $type, ?string $default = null, Array $attributes = [], Array $plain_attr = [], ...$args)
    {  
        if ($type != 'list'){
            $attributes['type']  = $type;
        }

        $plain_attr[] = is_null($default) ? '' : "value=\"$default\""; 
        
        return static::renderTag('input', null, $attributes, $plain_attr, ...$args);
    }

    static function text(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('text', $default, $attributes, ...$args);
    }

    static function password(?string $default = null, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('password', $default, $attributes, ...$args);
    }

    static function email(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('email', $default, $attributes, ...$args);
    }

    static function number(string $text = null,  Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('number', $text, $attributes, ...$args);
    }

    static function file(Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('file', null, $attributes, ...$args);
    }

    static function date(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('date', $default, $attributes, ...$args); 
    }

    static function month(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('month', $default, $attributes, ...$args); 
    }

    static function inputTime(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('time', $default, $attributes, ...$args); 
    }

    static function week(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('week', $default, $attributes, ...$args); 
    }

    static function datetimeLocal(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('datetime-local', $default, $attributes, ...$args); 
    }

    static function image(?string $default = null, Array $attributes = [], ...$args){
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        return static::input('image', $default, $attributes, ...$args); 
    }

    static function range(int $min, int $max, $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['min'] = $min;
        $attributes['max'] = $max;
        return static::input('range', $default, $attributes, ...$args); 
    }

    static function tel(string $pattern, Array $attributes = [], ...$args){
        $attributes['patern'] = $pattern;
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('tel', null, $attributes, ...$args); 
    }

    static function url(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('url', $default, $attributes, ...$args); 
    }

    static protected function label(string $id, string $placeholder, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['for'] = $id;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass('label') : static::getClass('label');
        return static::renderTag('label', $placeholder, $attributes);
    }

    // implementación especial 
    static function checkbox(?string $text = null,  bool $checked = false, Array $attributes = [], ...$args){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes['type']  = __FUNCTION__;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        return static::renderTag('input', $text, $attributes, $plain_attr, ...$args);
    }

    // implementación especial 
    static function radio(?string $text = null,  bool $checked = false, Array $attributes = [], ...$args){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes['type']  = __FUNCTION__;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $value = '';
        if (!empty($text)){
            if (isset($args['id'])){
                $attributes['id'] = $args['id'];
            }

            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = static::label($attributes['id'], $text);
        }

        return static::renderTag('input', $value, $attributes, $plain_attr, ...$args);
    }

    static function color(?string $text = null, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);

        $attributes['type']  = __FUNCTION__;
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $value = '';
        if (!empty($text)){
            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = static::label($attributes['id'], $text);
        }

        return static::renderTag('input', $value, $attributes);
    }

    static function area(?string $default = null, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::renderTag('textarea', $default, $attributes);
    }

    /*
        Form::select(name:'size', options:['L' => 'Large', 'S' => 'Small'], placeholder:Pick a size...']);

        Además acepta un agrupamiento de opciones en "secciones" o "categorías"

        Form::select(name:'comidas', options:[
        'platos' => [
            'Pasta' => 'pasta',
            'Pizza' => 'pizza',
            'Asado' => 'asado' 
        ],

        'frutas' => [
            'Banana' => 'banana',
            'Frutilla' => 'frutilla'
        ],         
        placeholder:'Escoja su comida favoria');


        Ver
        http://paulrose.com/bootstrap-select-sass/
    */
    static function select(Array $options, ?string $default = null, ?string $placeholder = null, Array $attributes = [], ...$args)
    {
        $attributes = array_merge($attributes, $args);
        $attributes['placeholder'] = $placeholder;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);

        $a2 = is_array(Arrays::array_value_first($options));

        // options
        $got_selected = false;

        if ($a2){
            $groups = '';
            foreach ($options as $grp){
                $_opt  = [];
                foreach ($grp as $opt => $val){
                    if ($val == $default){
                        $selected = 'selected';
                        $got_selected = true;
                    } else {
                        $selected = '';
                    }
    
                    $_opt[] = "<option value=\"$val\" $selected>$opt</option>";
                }
    
                if (!empty($placeholder)){
                    $selected = !$got_selected;
                    $_opt = array_merge(['<option hidden="hidden" selected="selected">'.$placeholder.'</option>'], $_opt);
                }
            
                $opt_str = implode(' ', $_opt);
                $groups .= static::renderTag('optgroup', $opt_str, ['label' => $opt]);
            }

            $opt_str = $groups;
        } else {     
            $_opt  = [];       
            foreach ($options as $opt => $val){
                if ($val == $default){
                    $selected = 'selected';
                    $got_selected = true;
                } else {
                    $selected = '';
                }

                $_opt[] = "<option value=\"$val\" $selected>$opt</option>";
            }

            if (!empty($placeholder)){
                $selected = !$got_selected;
                $_opt = array_merge(["<option $selected>$placeholder</option>"], $_opt);
            }
        
            $opt_str = implode(' ', $_opt);
        }

       
        return static::renderTag(__FUNCTION__, $opt_str, $attributes);
    }

    static function dataList(string $listName, Array $options, ?string $placeholder = null, ?string $label = '', Array $attributes = [], ...$args)
    {
        $attributes = array_merge($attributes, $args);
        $attributes['placeholder'] = $placeholder;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        $attributes['list'] = $listName;

        // options
        $_opt = [];
        foreach ($options as $val){
            $_opt[] = "<option value=\"$val\"/>";
        }
    
        $opt_str = implode(' ', $_opt);

        $datalist = static::renderTag(__FUNCTION__, $opt_str, ['id' => $listName]);
        $label_t  = !empty($label) ? static::label($attributes['id'], $label) : '';
        $input    = static::input('list', null, $attributes, ...$args);

        return $label_t . $input . $datalist;
    }

    static function inputButton(string $value, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass('__FUNCTION__') : static::getClass(__FUNCTION__);
        return static::input('button', $value, $attributes, ...$args);
    }

    static function submit(string $value, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('submit', $value, $attributes, ...$args);
    }

    static function reset(string $value, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('reset', $value, $attributes, ...$args);
    }

    static function search(Array $attributes  = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::input('search', null, $attributes, ...$args);
    }

    static  function fieldset(Array $content, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. static::getClass(__FUNCTION__) : static::getClass(__FUNCTION__);
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function hidden(string $value, Array $attributes = [], ...$args){
        return static::input('hidden', $value, $attributes, ...$args);
    }

       /*
        Form::macro('myField', function()
        {
            return '<input type="awesome">';
        });

        Calling A Custom Form Macro

        echo Form::myField();

    */
    static function macro(string $name, callable $render_fn){
        static::$macros[$name] = $render_fn;
    }

    static function __callStatic($method, $args){
        if (isset(static::$macros[$method])){
            return static::$macros[$method](...$args);
        }

        return static::renderTag($method, '', [], [], ...$args);
    }

    static function div(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function header(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function nav(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function main(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function section(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function article(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function aside(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function details(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function summary(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    function mark(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function picture(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function figure(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function figcaption(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    function time(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function footer(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function ol(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function ul(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function table(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    /*
        <blockquote>
        <p>Beware of bugs in the above code; I have only proved it correct, not tried it.” </p>
        <cite><a href="http://www-cs-faculty.stanford.edu/~uno/faq.html">Donald Knuth: Notes on the van Emde Boas construction of priority deques: An instructive use of recursion, March 29th, 1977</a>
        </blockquote>
    */
    static function blockquote(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function q(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function cite(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function code(Array $content, $attributes = [], ...$args){
        return static::group($content, __FUNCTION__, $attributes, ...$args);
    }

    static function _p(Array $content, $attributes = [], ...$args){
        return static::group($content, 'p', $attributes, ...$args);
    }

    static function _span(Array $content, $attributes = [], ...$args){
        return static::group($content, 'span', $attributes, ...$args);
    }

    static function button(Array $content, $attributes = [], ...$args){
        return static::group($content, 'span', $attributes, ...$args);
    }

    static function p(?string $text = '', Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return static::renderTag(__FUNCTION__, $text, $attributes);
    }
    
    static function span(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return static::renderTag(__FUNCTION__, $text, $attributes);
    }

    static function legend(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return static::renderTag(__FUNCTION__, $text, $attributes);
    }

    static function strong(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return static::renderTag(__FUNCTION__, $text, $attributes);
    }

    static function em(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return static::renderTag(__FUNCTION__, $text, $attributes);
    }

    static function h(int $size, string $text, Array $attributes = [], ...$args){
        if ($size <1 || $size > 6){
            throw new \InvalidArgumentException("Incorrect size for H tag. Given $size. Expected 1 to 6");
        }

        $attributes = array_merge($attributes, $args);
        return static::renderTag('h'. (string) $size, $text, $attributes, ...$args);
    }

    static function h1(string $text, Array $attributes = [], ...$args){
        return static::h(1, $text, $attributes, ...$args);
    }

    static function h2(string $text, Array $attributes = [], ...$args){
        return static::h(2, $text, $attributes, ...$args);
    }

    static function h3(string $text, Array $attributes = [], ...$args){
        return static::h(3, $text, $attributes, ...$args);
    }

    static function h4(string $text, Array $attributes = [], ...$args){
        return static::h(4, $text, $attributes, ...$args);
    }

    static function h5(string $text, Array $attributes = [], ...$args){
        return static::h(5, $text, $attributes, ...$args);
    }

    static function h6(string $text, Array $attributes = [], ...$args){
        return static::h(6, $text, $attributes, ...$args);
    }

    static function br(Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return static::renderTag(__FUNCTION__, null, $attributes);
    }

    static function img(string $src, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['src'] = $src;
        return static::renderTag('img', null, $attributes); 
    }

    /*
        https://github.com/vanilla/htmlawed
    */
    static function beautifier(string $html){  
        $config = ['tidy'=>1];
        return htmLawed($html, $config);;
    }

}

