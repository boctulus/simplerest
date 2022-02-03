<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Tag;

class Html
{
    protected $html  = '';
    protected $class = '';
    protected $pretty     = false;
    protected $id_eq_name = false;
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

        "label"          => "form-label",

        "submit"         => "btn btn-primary",
        "reset"          => "btn btn-primary",
        "inputButton"    => "btn btn-primary",

        "inputGroup"     => "input-group",
        "checkGroup"     => "form-check",

        "color"          => "form-control form-control-color"
    ];
    
    static protected $macros = [];

    function __construct() { }

    function prety(bool $state = true){
        $this->pretty = $state;
        return $this;
    }

    /*
        Copy name attribute into id one
    */
    function setIdAsName(bool $state = true){
        $this->id_eq_name = $state;

        return $this;
    }

    protected function add(string $html){
        $this->html .= ' '. $html;
        return $this;
    }

    protected function attributes(Array $attributes) : string{
        $_att = [];
        foreach ($attributes as $att => $val){
            $_att[] = "$att=\"$val\"";
        }

        return implode(' ', $_att);
    }

    public function getClass(string $tag) : string{
        return $this->classes[$tag] ?? '';
    }

    protected function renderTag(string $type, ?string $value = '', Array $attributes = [], Array $plain_attr = [], ...$args) : string
    {
        if (isset($attributes['class']) && isset($args['class'])){
            $attributes['class'] .=  ' ' . $args['class'];
            unset($args['class']);
        }

        $attributes = array_merge($attributes, $args);

        if (!empty($this->class)){
            $attributes['class'] .= ' ' . $this->class;
        } 

        $name = $attributes['name'] ?? '';

        if (!empty($name) && $this->id_eq_name){
            $attributes['id'] = $name;
        }

        $att_str = $this->attributes($attributes);
        $p_atr   = implode(' ', $plain_attr);

        // en principio asumo que abre y cierra
        return "<$type $att_str $p_atr>$value</$type>";
    }

    function tag(string $type, ?string $value = '', Array $attributes = [], Array $plain_attr = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag($type, $value, $attributes, $plain_attr));
    }

    function link_to(string $url, string $anchor, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);

        if (Strings::startsWith('www.', $url)){
            $url = "http://$url";
        }

        return $this->add($this->renderTag('a', $anchor, $attributes));
    }


    function input(string $type, ?string $default = null, Array $attributes = [], Array $plain_attr = [], ...$args)
    {  
        $attributes['type']  = $type;
        $plain_attr[] = is_null($default) ? '' : "value=\"$default\""; 
        
        return $this->add($this->renderTag('input', null, $attributes, $plain_attr, ...$args));
    }

    function text(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('text', $default, $attributes, ...$args);
    }

    function password(?string $default = null, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('password', $default, $attributes, ...$args);
    }

    function email(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('email', $default, $attributes, ...$args);
    }

    function number(string $text = null,  Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('number', $text, $attributes, ...$args);
    }

    function file(Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('file', null, $attributes, ...$args);
    }

    function date(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('date', $default, $attributes, ...$args); 
    }

    function month(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('month', $default, $attributes, ...$args); 
    }

    function inputTime(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('time', $default, $attributes, ...$args); 
    }

    function week(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('week', $default, $attributes, ...$args); 
    }

    function datetimeLocal(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('datetime-local', $default, $attributes, ...$args); 
    }

    function image(?string $default = null, Array $attributes = [], ...$args){
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        return $this->input('image', $default, $attributes, ...$args); 
    }

    function range(int $min, int $max, $default = null, Array $attributes = [], ...$args){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        $attributes['min'] = $min;
        $attributes['max'] = $max;
        return $this->input('range', $default, $attributes, ...$args); 
    }

    function tel(string $pattern, Array $attributes = [], ...$args){
        $attributes['patern'] = $pattern;
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('tel', null, $attributes, ...$args); 
    }

    function url(?string $default = null, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('url', $default, $attributes, ...$args); 
    }

    protected function __label(string $id, string $placeholder, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['for'] = $id;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass('label') : $this->getClass('label');
        return $this->renderTag('label', $placeholder, $attributes);
    }

    function label(string $id, string $placeholder, Array $attributes = [], ...$args){;
        return $this->add($this->__label($id, $placeholder, $attributes, ...$args));
    }

    // implementación especial 
    function checkbox(?string $text = null,  bool $checked = false, Array $attributes = [], ...$args){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes = array_merge($attributes, $args);
        $attributes['type']  = __FUNCTION__;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        return $this->add($this->renderTag('input', $text, $attributes, $plain_attr, ...$args));
    }

    // implementación especial 
    function radio(?string $text = null,  bool $checked = false, Array $attributes = [], ...$args){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes = array_merge($attributes, $args);
        $attributes['type']  = __FUNCTION__;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        $value = '';
        if (!empty($text)){
            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = $this->__label($attributes['id'], $text);
        }

        return $this->add($this->renderTag('input', $value, $attributes, $plain_attr));
    }

    function color(?string $text = null, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);

        $attributes['type']  = __FUNCTION__;
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        $value = '';
        if (!empty($text)){
            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = $this->__label($attributes['id'], $text);
        }

        return $this->add($this->renderTag('input', $value, $attributes));
    }

    function area(?string $default = null, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->add($this->renderTag('textarea', $default, $attributes));
    }

    /*
        Form::select('size', ['L' => 'Large', 'S' => 'Small'], null, ['placeholder' => 'Pick a size...']);

        Debe aceptar un agrupamiento de opciones en "secciones" o "categorías"

        Form::select('animal',[
            'Cats' => ['leopard' => 'Leopard'],
            'Dogs' => ['spaniel' => 'Spaniel'],
        ])
    */
    function select(Array $options, ?string $default = null, ?string $placeholder = null, Array $attributes = [], ...$args)
    {
        $attributes = array_merge($attributes, $args);
        $attributes['placeholder'] = $placeholder;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        // options
        $_opt = [];

        $got_selected = false;
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

        return $this->add($this->renderTag(__FUNCTION__, $opt_str, $attributes));
    }

    function inputButton(string $value, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass('__FUNCTION__') : $this->getClass(__FUNCTION__);
        return $this->input('button', $value, $attributes, ...$args);
    }

    function submit(string $value, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('submit', $value, $attributes, ...$args);
    }

    function reset(string $value, Array $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('reset', $value, $attributes, ...$args);
    }

    function search(Array $attributes  = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('search', null, $attributes, ...$args);
    }

    function fieldset(callable $closure, $attributes = [], ...$args){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function hidden(string $value, Array $attributes = [], ...$args){
        return $this->input('hidden', $value, $attributes, ...$args);
    }

    function group(callable $closure, string $tag = 'div', Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);

        $f = new Form();
        call_user_func($closure, $f);
        
        return $this->add($f->render($tag, $attributes));
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
    }

    function insert(string $html){
        return $this->add($html);
    }

    // alias for insert()
    function string(string $text){
        return $this->insert($text);
    }

    function div(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function header(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function nav(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function main(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function section(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function article(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function aside(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function details(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function summary(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function mark(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function picture(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function figure(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function figcaption(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function time(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function footer(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function ol(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function ul(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function table(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    /*
        <blockquote>
        <p>Beware of bugs in the above code; I have only proved it correct, not tried it.” </p>
        <cite><a href="http://www-cs-faculty.stanford.edu/~uno/faq.html">Donald Knuth: Notes on the van Emde Boas construction of priority deques: An instructive use of recursion, March 29th, 1977</a>
        </blockquote>
    */
    function blockquote(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function q(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function cite(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function code(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, __FUNCTION__, $attributes, ...$args);
    }

    function _p(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, 'p', $attributes, ...$args);
    }

    function _span(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, 'span', $attributes, ...$args);
    }

    function button(callable $closure, $attributes = [], ...$args){
        return $this->group($closure, 'span', $attributes, ...$args);
    }

    function p(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag(__FUNCTION__, $text, $attributes));
    }
    
    function span(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag(__FUNCTION__, $text, $attributes));
    }

    function legend(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag(__FUNCTION__, $text, $attributes));
    }

    function strong(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag(__FUNCTION__, $text, $attributes));
    }

    function em(string $text, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag(__FUNCTION__, $text, $attributes));
    }

    function h(int $size, string $text, Array $attributes = [], ...$args){
        if ($size <1 || $size > 6){
            throw new \InvalidArgumentException("Incorrect size for H tag. Given $size. Expected 1 to 6");
        }

        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag('h'. (string) $size, $text, $attributes, ...$args));
    }

    function h1(string $text, Array $attributes = [], ...$args){
        return $this->h(1, $text, $attributes, ...$args);
    }

    function h2(string $text, Array $attributes = [], ...$args){
        return $this->h(1, $text, $attributes, ...$args);
    }

    function h3(string $text, Array $attributes = [], ...$args){
        return $this->h(1, $text, $attributes, ...$args);
    }

    function h4(string $text, Array $attributes = [], ...$args){
        return $this->h(1, $text, $attributes, ...$args);
    }

    function h5(string $text, Array $attributes = [], ...$args){
        return $this->h(1, $text, $attributes, ...$args);
    }

    function h6(string $text, Array $attributes = [], ...$args){
        return $this->h(1, $text, $attributes, ...$args);
    }

    function br(Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        return $this->add($this->renderTag(__FUNCTION__, null, $attributes));
    }

    function img(string $src, Array $attributes = [], ...$args){
        $attributes = array_merge($attributes, $args);
        $attributes['src'] = $src;
        return $this->add($this->renderTag('img', null, $attributes)); 
    }

    // NO usar..........  hace un desastre volviendo a incluid head
    static function beautifier(string $html){
        $config = array(
        'indent'         => true,
        'output-xhtml'   => true,
        'wrap'           => 200);
    
        // Tidy
        $tidy = new \tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();

        ob_start();
        echo $tidy;
        return ob_get_clean();
    }

    function render(?string $enclosingTag = '', Array $attributes = [], ...$args) : string {
        $ret = $this->html;

        if (isset($attributes['class']) && isset($args['class'])){
            $attributes['class'] .=  ' ' . $args['class'];
            unset($args['class']);
        }

        $attributes = array_merge($attributes, $args);

        if (!empty($enclosingTag)){
            $att = $this->attributes($attributes);
            $ret = "<$enclosingTag $att>{$ret}</$enclosingTag>";
        }
        
        return $this->pretty ? static::beautifier($ret) : $ret;
    }

}

