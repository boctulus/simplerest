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
    protected $pretty = false;

    function __construct(?string $url = null, ?string $method = null) {
        $this->url = $url;
        $this->method = $method;
    }

    function prety(bool $state){
        $this->pretty = $state;
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

    function input(string $type, ?string $name = null, ?string $default_value = null, Array $attributes = [])
    {   
        $value    = is_null($default_value) ? '' : "value=\"$default_value\""; 
        $the_name = is_null($name) ? '' : "name=\"$name\"";

        $_att = [];
        foreach ($attributes as $att => $val){
            $_att[] = "$att=\"$val\"";
        }

        $att_str = implode(' ', $_att);
        return $this->add("<input type=\"$type\" $the_name $value $att_str>");
    }

    function text(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('text', $name, $default_value, $attributes);
    }

    function password(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('password', $name, $default_value, $attributes);
    }

    function email(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('email', $name, $default_value, $attributes);
    }

    function number(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('number', $name, $default_value, $attributes);
    }

    function checkbox(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('checkbox', $name, $default_value, $attributes);
    }

    function radio(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('radio', $name, $default_value, $attributes); 
    }

    function file(string $name, Array $attributes = []){
        return $this->input('file', $name, null, $attributes);
    }

    function color(string $name, string $value = null, Array $attributes = []){
        return $this->input('color', $name, $value, $attributes); 
    }

    function date(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('date', $name, $default_value, $attributes); 
    }

    function month(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('month', $name, $default_value, $attributes); 
    }

    function time(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('time', $name, $default_value, $attributes); 
    }

    function week(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('week', $name, $default_value, $attributes); 
    }

    function datetime_local(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('datetime-local', $name, $default_value, $attributes); 
    }

    function image(string $name, ?string $default_value = null, Array $attributes = []){
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        return $this->input('image', $name, $default_value, $attributes); 
    }

    function range(string $name, int $min, int $max, $default_value = null){
        return $this->input('range', $name, $default_value, [
            'min' => $min,
            'max' => $max
        ]); 
    }

    function tel(string $name, string $pattern, Array $attributes = []){
        $attributes = array_merge($attributes, [ 'patern' => $pattern ]);
        return $this->input('tel', $name, null, $attributes); 
    }

    function url(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('url', $name, $default_value, $attributes); 
    }

    function hidden(string $name, string $value, Array $attributes = []){
        return $this->input('hidden', $name, $value, $attributes);
    }

    function button(string $name, string $value, Array $attributes = []){
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

    function area(string $name, ?string $default_value = null, Array $attributes = []){
        $value = $default_value ?? '';    
        $att_str = $this->attributes($attributes);

        return $this->add("<textarea $name $att_str>$value</textarea>");
    }

    /*
        Form::select('size', ['L' => 'Large', 'S' => 'Small'], null, ['placeholder' => 'Pick a size...']);

        Debe aceptar un agrupamiento de opciones en "secciones" o "categorías"

        Form::select('animal',[
            'Cats' => ['leopard' => 'Leopard'],
            'Dogs' => ['spaniel' => 'Spaniel'],
        ])
    */
    function select(string $name, ?string $default_value = null, Array $options, ?string $placeholder = null, Array $attributes = []){
        $value    = is_null($default_value) ? '' : "value=\"$default_value\""; 
        $the_name = is_null($name) ? '' : "name=\"$name\"";

        $_att = [];
        foreach ($attributes as $att => $val){
            $_att[] = "$att=\"$val\"";
        }

        $att_str = implode(' ', $_att);

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

        return $this->add("<select $the_name $att_str>$opt_str</select>");
    }

    function label(string $name, string $placeholder, Array $attributes = []){
        $att = $this->attributes($attributes);
        return $this->add("<label for=\"$name\" $att>$placeholder</label>");
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

        return $tidy;
    }

    function render(){
        $ret = "<form>{$this->html}</form>";
        return $this->pretty ? static::beautifier($ret) : $ret;
    }

}

