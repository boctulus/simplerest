<?php

namespace simplerest\core\libs;

/*
    Falta decorar con las clases de Boostrap 5
*/
class Form extends Html
{
    protected $url;
    protected $method;
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

        "label"          => "form-check-label",

        "submit"         => "btn btn-primary",
        "reset"          => "btn btn-primary",
        "inputButton"    => "btn btn-primary",

        "inputGroup"     => "input-group",
        "checkGroup"     => "form-check"
    ];

    function __construct(?string $url = null, ?string $method = null) {
        $this->url = $url;
        $this->method = $method;
    }

    function setUrl(string $url){
        $this->url = $url;
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

    function color(string $name, string $text, Array $attributes = []){
        $attributes['class']  = isset($attributes['class']) ? $attributes['class'] . ' '. $this->getClass(__FUNCTION__) : $this->getClass(__FUNCTION__);
        return $this->input('color', $name, $text, $attributes); 
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

    function render(string $enclosing_tag = null, Array $attributes = [], bool $pretty = true) : string {
        if (is_null($enclosing_tag)){
            $enclosing_tag = 'form';
        }

        return parent::render($enclosing_tag, $attributes, $pretty);
    }
}

