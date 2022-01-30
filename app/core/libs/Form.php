<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

/*
    Falta decorar con las clases de Boostrap 5
*/
class Form
{
    protected $url;
    protected $method;
    protected $html;
    protected $class = '';
    protected $pretty = false;
    protected $id_as_name = true;

    function __construct(?string $url = null, ?string $method = null) {
        $this->url = $url;
        $this->method = $method;
    }

    function prety(bool $state){
        $this->pretty = $state;
    }

    function idAsName(bool $state){
        $this->id_as_name = $state;
    }

    function setUrl(string $url){
        $this->url = $url;
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

    public function __call($method, $args){
        $class = [
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

            "button"         => "btn btn-primary"
        ];

        if (isset($class[$method])){
            $this->class = $class[$method];
        } else {
            $this->class = '';
        }

        return call_user_func_array(array($this, $method), $args);
    }

    protected function input(string $type, ?string $name = null, ?string $default_value = null, Array $attributes = [], Array $plain_attr = [])
    {   
        if (!is_null($name)){
            if ($this->id_as_name){
                $attributes['id'] = $name;
            } else {
                $attributes['name'] = $name;
            }   
        }

        if (!empty($this->class) && isset($attributes['class'])){
            $attributes['class'] .= ' ' . $this->class;
        } else {
            $attributes['class'] = ' ' . $this->class;
        }

        $value = is_null($default_value) ? '' : "value=\"$default_value\""; 

        $att_str = $this->attributes($attributes);
        $p_atr   = implode(' ', $plain_attr);

        return $this->add("<input type=\"$type\" $value $att_str $p_atr>");
    }

    protected function text(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('text', $name, $default_value, $attributes);
    }

    protected function password(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('password', $name, $default_value, $attributes);
    }

    protected function email(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('email', $name, $default_value, $attributes);
    }

    protected function number(string $name, string $text = null,  Array $attributes = []){
        return $this->input('number', $name, $text, $attributes);
    }

    protected function checkbox(string $name, string $text,  bool $checked = false, Array $attributes = []){
        if (!is_null($name)){
            if ($this->id_as_name){
                $attributes['id'] = $name;
            } else {
                $attributes['name'] = $name;
            }   
        }

        if (!empty($this->class) && isset($attributes['class'])){
            $attributes['class'] .= ' ' . $this->class;
        } else {
            $attributes['class'] = ' ' . $this->class;
        }

        $att_str = $this->attributes($attributes);
        $chk     = $checked ? 'checked' : '';
        return $this->add("<input type=\"checkbox\" $chk $att_str>$text</input>");
    }

    protected function radio(string $name, string $text,  bool $checked = false, Array $attributes = []){
        if (!is_null($name)){
            if ($this->id_as_name){
                $attributes['id'] = $name;
            } else {
                $attributes['name'] = $name;
            }   
        }

        if (!empty($this->class) && isset($attributes['class'])){
            $attributes['class'] .= ' ' . $this->class;
        } else {
            $attributes['class'] = ' ' . $this->class;
        }

        $att_str = $this->attributes($attributes);
        $chk     = $checked ? 'checked' : '';
        return $this->add("<input type=\"radio\" $chk $att_str>$text</input>");
    }

    protected function file(string $name, Array $attributes = []){
        return $this->input('file', $name, null, $attributes);
    }

    protected function color(string $name, string $text, Array $attributes = []){
        return $this->input('color', $name, $text, $attributes); 
    }

    function date(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('date', $name, $default_value, $attributes); 
    }

    protected function month(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('month', $name, $default_value, $attributes); 
    }

    protected function time(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('time', $name, $default_value, $attributes); 
    }

    protected function week(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('week', $name, $default_value, $attributes); 
    }

    protected function datetime_local(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('datetime-local', $name, $default_value, $attributes); 
    }

    protected function image(string $name, ?string $default_value = null, Array $attributes = []){
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        return $this->input('image', $name, $default_value, $attributes); 
    }

    protected function range(string $name, int $min, int $max, $default_value = null){
        return $this->input('range', $name, $default_value, [
            'min' => $min,
            'max' => $max
        ]); 
    }

    protected function tel(string $name, string $pattern, Array $attributes = []){
        $attributes = array_merge($attributes, [ 'patern' => $pattern ]);
        return $this->input('tel', $name, null, $attributes); 
    }

    protected function url(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('url', $name, $default_value, $attributes); 
    }

    protected function area(string $name, ?string $default_value = null, Array $attributes = []){
        if (!is_null($name)){
            if ($this->id_as_name){
                $attributes['id'] = $name;
            } else {
                $attributes['name'] = $name;
            }   
        }

        if (!empty($this->class) && isset($attributes['class'])){
            $attributes['class'] .= ' ' . $this->class;
        } else {
            $attributes['class'] = ' ' . $this->class;
        }

        $value = $default_value ?? '';    
        $att_str = $this->attributes($attributes);

        return $this->add("<textarea $att_str>$value</textarea>");
    }

    /*
        Form::select('size', ['L' => 'Large', 'S' => 'Small'], null, ['placeholder' => 'Pick a size...']);

        Debe aceptar un agrupamiento de opciones en "secciones" o "categorías"

        Form::select('animal',[
            'Cats' => ['leopard' => 'Leopard'],
            'Dogs' => ['spaniel' => 'Spaniel'],
        ])
    */
    protected function select(string $name, ?string $default_value = null, Array $options, ?string $placeholder = null, Array $attributes = [])
    {
        if (!is_null($name)){
            if ($this->id_as_name){
                $attributes['id'] = $name;
            } else {
                $attributes['name'] = $name;
            }   
        }

        if (!empty($this->class) && isset($attributes['class'])){
            $attributes['class'] .= ' ' . $this->class;
        } else {
            $attributes['class'] = ' ' . $this->class;
        }

        $att_str = $this->attributes($attributes);

        // options
        $_opt = [];
        foreach ($options as $opt => $val){
            if ($val == $default_value){
                $selected = 'selected';
            } else {
                $selected = '';
            }

            $_opt[] = "<option value=\"$val\" $selected\">$opt</option>";
        }

        $opt_str = implode(' ', $_opt);

        return $this->add("<select $att_str>$opt_str</select>");
    }

    protected function label(string $id, string $placeholder, Array $attributes = []){
        $att = $this->attributes($attributes);
        return $this->add("<label for=\"$id\" $att>$placeholder</label>");
    }

    protected function button(string $name, string $value, Array $attributes = []){
        return $this->input('button', $name, $value, $attributes);
    }

    function submit(string $name, string $value, Array $attributes = []){
        return $this->input('submit', $name, $value, $attributes);
    }

    function reset(string $name, string $value, Array $attributes = []){
        return $this->input('reset', $name, $value, $attributes);
    }

    function search(string $name, Array $attributes  = []){
        return $this->input('search', $name, null, $attributes);
    }


    function hidden(string $name, string $value, Array $attributes = []){
        return $this->input('hidden', $name, $value, $attributes);
    }

    function link_to(string $url, string $anchor, Array $attributes = []){
        if (Strings::startsWith('www.', $url)){
            $url = "http://$url";
        }

        $att = $this->attributes($attributes);
        return $this->add("<a href=\"$url\" $att>$anchor</a>");
    }

    /*
        Form::macro('myField', function()
        {
            return '<input type="awesome">';
        });

        Calling A Custom Form Macro

        echo Form::myField();

    */
    function macro(string $name, callable $render_fn){

    }

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

    function render(){
        $ret = "<form>{$this->html}</form>";
        return $this->pretty ? static::beautifier($ret) : $ret;
    }

}

