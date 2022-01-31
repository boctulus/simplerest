<?php

namespace simplerest\core\libs;


class Html
{
    protected $html  = '';
    protected $class = '';
    protected $pretty     = true;
    protected $id_as_name = false;
    protected $id_eq_name = true;
    
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
    }

    /*
        Copy name attribute into id one
    */
    function setIdAsName(bool $state = true){
        $this->id_eq_name = $state;

        if ($this->id_eq_name){
            $this->id_as_name = false;
        }
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

    protected function renderTag(string $type, ?string $name = null, ?string $value = '', Array $attributes = [], Array $plain_attr = [])
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
        
        $attributes['class'] = $attributes['class'] ?? '';

        if (!empty($this->class)){
            $attributes['class'] .= ' ' . $this->class;
        } 

        $att_str = $this->attributes($attributes);
        $p_atr   = implode(' ', $plain_attr);

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

        $att = $this->attributes($attributes);
        return $this->add("<a href=\"$url\" $att>$anchor</a>");
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

    function render(string $enclosingrenderTag = null, Array $attributes = [], bool $pretty = true) : string {
        $ret = $this->html;

        if (!empty($enclosingrenderTag)){
            $att = $this->attributes($attributes);
            $ret = "<$enclosingrenderTag $att>{$ret}</$enclosingrenderTag>";
        }
        
        return ($pretty && $this->pretty) ? static::beautifier($ret) : $ret;
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
        if (!isset($attributes['src'])){
            throw new \Exception("src attribute is required");
        }

        return $$this->add("<img src=\"$src\">"); 
    }
}

