<?php

namespace simplerest\core\libs;


class Html
{
    protected $html  = '';
    protected $class = '';
    protected $pretty     = false;
    protected $id_as_name = false;
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

    function prety(bool $state){
        $this->pretty = $state;
    }

    /*
        Use Id instead of name attribute
    */
    function useIdAsName(bool $state = true){
        $this->id_as_name = $state;

        if ($this->id_as_name){
            $this->id_eq_name = false;
        }

        return $this;
    }

    /*
        Copy name attribute into id one
    */
    function setIdAsName(bool $state = true){
        $this->id_eq_name = $state;

        if ($this->id_eq_name){
            $this->id_as_name = false;
        }

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

    protected function renderTag(string $type, ?string $name = null, ?string $value = '', Array $attributes = [], Array $plain_attr = []) : string
    {   
        if (!is_null($name)){
            if ($this->id_eq_name){
                $attributes['id'] = $name;
                $attributes['name'] = $name;
            } else if ($this->id_as_name){
                $attributes['id'] = $name;
            } else {
                $attributes['name'] = $name;
            }   
        }

        //$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass($type) : $this->getClass($type);

        if (!empty($this->class)){
            $attributes['class'] .= ' ' . $this->class;
        } 

        $att_str = $this->attributes($attributes);
        $p_atr   = implode(' ', $plain_attr);

        //d($att_str, 'att_str');

        // en principio asumo que abre y cierra
        return "<$type $att_str $p_atr>$value</$type>";
    }

    function tag(string $type, ?string $name = null, ?string $value = '', Array $attributes = [], Array $plain_attr = []){
        return $this->add($this->renderTag($type, $name, $value, $attributes, $plain_attr));
    }

    function link_to(string $url, string $anchor, Array $attributes = []){
        if (Strings::startsWith('www.', $url)){
            $url = "http://$url";
        }

        return $this->add($this->renderTag('a', null, $anchor, $attributes));
    }


    function input(string $type, ?string $name = null, ?string $default_value = null, Array $attributes = [], Array $plain_attr = [])
    {  
        $plain_attr[] = is_null($default_value) ? '' : "value=\"$default_value\""; 
        $attributes['type']  = $type;
        return $this->add($this->renderTag('input', $name, null, $attributes, $plain_attr));
    }

    function text(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('text', $name, $default_value, $attributes);
    }

    function password(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('password', $name, $default_value, $attributes);
    }

    function email(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('email', $name, $default_value, $attributes);
    }

    function number(string $name, string $text = null,  Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('number', $name, $text, $attributes);
    }

    function file(string $name, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('file', $name, null, $attributes);
    }

    function date(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('date', $name, $default_value, $attributes); 
    }

    function month(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('month', $name, $default_value, $attributes); 
    }

    function inputTime(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('time', $name, $default_value, $attributes); 
    }

    function week(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('week', $name, $default_value, $attributes); 
    }

    function datetimeLocal(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('datetime-local', $name, $default_value, $attributes); 
    }

    function image(string $name, ?string $default_value = null, Array $attributes = []){
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        return $this->input('image', $name, $default_value, $attributes); 
    }

    function range(string $name, int $min, int $max, $default_value = null, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        $attributes['min'] = $min;
        $attributes['max'] = $max;
        return $this->input('range', $name, $default_value, $attributes); 
    }

    function tel(string $name, string $pattern, Array $attributes = []){
        $attributes['patern'] = $pattern;
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('tel', $name, null, $attributes); 
    }

    function url(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('url', $name, $default_value, $attributes); 
    }

    protected function __label(string $id, string $placeholder, Array $attributes = []){
        $attributes['for'] = $id;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass('label') : $this->getClass('label');
        return $this->renderTag('label', null, $placeholder, $attributes);
    }

    function label(string $id, string $placeholder, Array $attributes = []){;
        return $this->add($this->__label($id, $placeholder, $attributes));
    }

    // implementación especial 
    function checkbox(string $name, string $text,  bool $checked = false, Array $attributes = []){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes['type']  = __FUNCTION__;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        return $this->add($this->renderTag('input', $name, $text, $attributes, $plain_attr));
    }

    // implementación especial 
    function radio(string $name, ?string $text = null,  bool $checked = false, Array $attributes = []){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes['type']  = __FUNCTION__;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        $value = '';
        if (!empty($text)){
            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = $this->__label($attributes['id'], $text);
        }

        return $this->add($this->renderTag('input', $name, $value, $attributes, $plain_attr));
    }

    function color(string $name, ?string $text = null, Array $attributes = []){
        $attributes['type']  = __FUNCTION__;
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        $value = '';
        if (!empty($text)){
            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = $this->__label($attributes['id'], $text);
        }

        return $this->add($this->renderTag('input', $name, $value, $attributes));
    }

    function area(string $name, ?string $default_value = null, Array $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->add($this->renderTag('textarea', $name, $default_value, $attributes));
    }

    /*
        Form::select('size', ['L' => 'Large', 'S' => 'Small'], null, ['placeholder' => 'Pick a size...']);

        Debe aceptar un agrupamiento de opciones en "secciones" o "categorías"

        Form::select('animal',[
            'Cats' => ['leopard' => 'Leopard'],
            'Dogs' => ['spaniel' => 'Spaniel'],
        ])
    */
    function select(string $name, Array $options, ?string $default_value = null, ?string $placeholder = null, Array $attributes = [])
    {
        $attributes['placeholder'] = $placeholder;
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);

        // options
        $_opt = [];

        $got_selected = false;
        foreach ($options as $opt => $val){
            if ($val == $default_value){
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

        return $this->add($this->renderTag(__FUNCTION__, $name, $opt_str, $attributes));
    }

    function inputButton(string $name, string $value, Array $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass('__FUNCTION__') : $this->getClass(__FUNCTION__);
        return $this->input('button', $name, $value, $attributes);
    }

    function submit(string $name, string $value, Array $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('submit', $name, $value, $attributes);
    }

    function reset(string $name, string $value, Array $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('reset', $name, $value, $attributes);
    }

    function search(string $name, Array $attributes  = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('search', $name, null, $attributes);
    }

    function fieldset(callable $closure, $attributes = []){
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function hidden(string $name, string $value, Array $attributes = []){
        return $this->input('hidden', $name, $value, $attributes);
    }

    function group(callable $closure, string $tag = 'div', Array $attributes = []){
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

    function div(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function header(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function nav(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function main(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function section(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function article(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function aside(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function details(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function summary(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function mark(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function picture(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function figure(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function figcaption(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function time(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function footer(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function ol(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function ul(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function table(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    /*
        <blockquote>
        <p>Beware of bugs in the above code; I have only proved it correct, not tried it.” </p>
        <cite><a href="http://www-cs-faculty.stanford.edu/~uno/faq.html">Donald Knuth: Notes on the van Emde Boas construction of priority deques: An instructive use of recursion, March 29th, 1977</a>
        </blockquote>
    */
    function blockquote(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function q(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function cite(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function code(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function _p(callable $closure, $attributes = []){
        return $this->group($closure, 'p', $attributes);
    }

    function _span(callable $closure, $attributes = []){
        return $this->group($closure, 'span', $attributes);
    }

    function button(callable $closure, $attributes = []){
        return $this->group($closure, 'span', $attributes);
    }

    function p(string $text, Array $attributes = []){
        return $this->add($this->renderTag(__FUNCTION__, null, $text, $attributes));
    }
    
    function span(string $text, Array $attributes = []){
        return $this->add($this->renderTag(__FUNCTION__, null, $text, $attributes));
    }

    function legend(string $text, Array $attributes = []){
        return $this->add($this->renderTag(__FUNCTION__, null, $text, $attributes));
    }

    function strong(string $text, Array $attributes = []){
        return $this->add($this->renderTag(__FUNCTION__, null, $text, $attributes));
    }

    function em(string $text, Array $attributes = []){
        return $this->add($this->renderTag(__FUNCTION__, null, $text, $attributes));
    }

    function h(int $size = 3, string $text, Array $attributes = []){
        if ($size <1 || $size > 6){
            throw new \InvalidArgumentException("Incorrect size for H tag. Given $size. Expected 1 to 6");
        }

        return $this->add($this->renderTag('h'. (string) $size, null, $text, $attributes));
    }

    function h1(string $text, Array $attributes = []){
        return $this->h(1, $text, $attributes);
    }

    function h2(string $text, Array $attributes = []){
        return $this->h(1, $text, $attributes);
    }

    function h3(string $text, Array $attributes = []){
        return $this->h(1, $text, $attributes);
    }

    function h4(string $text, Array $attributes = []){
        return $this->h(1, $text, $attributes);
    }

    function h5(string $text, Array $attributes = []){
        return $this->h(1, $text, $attributes);
    }

    function h6(string $text, Array $attributes = []){
        return $this->h(1, $text, $attributes);
    }

    function br(Array $attributes = []){
        return $this->add($this->renderTag(__FUNCTION__, null, null, $attributes));
    }

    function img(string $src, Array $attributes = []){
        $attributes['src'] = $src;
        return $this->add($this->renderTag('img', null, null, $attributes)); 
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

    function render(string $enclosingrenderTag = null, Array $attributes = [], bool $pretty = true) : string {
        $ret = $this->html;

        if (!empty($enclosingrenderTag)){
            $att = $this->attributes($attributes);
            $ret = "<$enclosingrenderTag $att>{$ret}</$enclosingrenderTag>";
        }
        
        return ($pretty && $this->pretty) ? static::beautifier($ret) : $ret;
    }

}

