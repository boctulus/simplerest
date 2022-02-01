<?php

namespace simplerest\core\libs;

/*
    Falta decorar con las clases de Boostrap 5
*/
class Form extends Html
{
    protected $url;
    protected $method;
    
    function __construct(?string $url = null, ?string $method = null) {
        $this->url = $url;
        $this->method = $method;
    }

    function setUrl(string $url){
        $this->url = $url;
    }

    /*
        Se puede buguear el control llamado no existe.
        Fixear
    */
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

            // "checkbox"       => "form-check-input" ,
            // "radio"          => "form-check-input" ,

            "label"         => "form-check-label",

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
        $plain_attr[] = is_null($default_value) ? '' : "value=\"$default_value\""; 
        $attributes['type']  = $type;
        return $this->add($this->renderTag('input', $name, null, $attributes, $plain_attr));
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

    protected function inputTime(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('time', $name, $default_value, $attributes); 
    }

    protected function week(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('week', $name, $default_value, $attributes); 
    }

    protected function datetimeLocal(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('datetime-local', $name, $default_value, $attributes); 
    }

    protected function image(string $name, ?string $default_value = null, Array $attributes = []){
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        return $this->input('image', $name, $default_value, $attributes); 
    }

    protected function range(string $name, int $min, int $max, $default_value = null, Array $attributes = []){
        $attributes['min'] = $min;
        $attributes['max'] = $max;
        return $this->input('range', $name, $default_value, $attributes); 
    }

    protected function tel(string $name, string $pattern, Array $attributes = []){
        $attributes = array_merge($attributes, [ 'patern' => $pattern ]);
        return $this->input('tel', $name, null, $attributes); 
    }

    protected function url(string $name, ?string $default_value = null, Array $attributes = []){
        return $this->input('url', $name, $default_value, $attributes); 
    }

    protected function __label(string $id, string $placeholder, Array $attributes = []){
        $attributes['for'] = $id;
        return $this->renderTag('label', null, $placeholder, $attributes);
    }

    protected function label(string $id, string $placeholder, Array $attributes = []){
        return $this->add($this->__label($id, $placeholder, $attributes));
    }

    // implementación especial 
    protected function checkbox(string $name, string $text,  bool $checked = false, Array $attributes = []){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes['type']  = __FUNCTION__;
        return $this->add($this->renderTag('input', $name, $text, $attributes, $plain_attr));
    }

    // implementación especial 
    protected function radio(string $name, ?string $text = null,  bool $checked = false, Array $attributes = []){
        $plain_attr = $checked ?  ['checked'] : [];
        $attributes['type']  = __FUNCTION__;

        $value = '';
        if (!empty($text)){
            if (empty($attributes['id'])){
                throw new \Exception("With radio and placeholder then id is required");
            }

            $value = $this->__label($attributes['id'], $text);
        }

        return $this->add($this->renderTag('input', $name, $value, $attributes, $plain_attr));
    }

    protected function area(string $name, ?string $default_value = null, Array $attributes = []){
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
    protected function select(string $name, Array $options, ?string $default_value = null, ?string $placeholder = null, Array $attributes = [])
    {
        $attributes['placeholder'] = $placeholder;

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

    protected function inputButton(string $name, string $value, Array $attributes = []){
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

    function fieldset(callable $closure, $attributes = []){
        return $this->group($closure, __FUNCTION__, $attributes);
    }

    function hidden(string $name, string $value, Array $attributes = []){
        return $this->input('hidden', $name, $value, $attributes);
    }

    function render(string $enclosing_tag = null, Array $attributes = [], bool $pretty = true) : string {
        if (is_null($enclosing_tag)){
            $enclosing_tag = 'form';
        }

        return parent::render($enclosing_tag, $attributes, $pretty);
    }
}

