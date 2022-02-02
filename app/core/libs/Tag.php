<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\Arrayable;
use simplerest\core\interfaces\IMyArrayAccess;

class Tag
{
    protected $name;
    protected $props = [];
    protected $args;

    static protected $builder;

    function __construct(string $name) {
        if (static::$builder == false){
            throw new \Exception("Please register the HTML builder first");
        }

        $this->name = $name;
    }

    static function registerBuilder(string $builder){
        static::$builder = $builder;
    }

    public function __set(string $name, mixed $value): void{
        $this->props[$name] = $value;
    }

    public function __get(string $name): mixed{
        return $this->props[$name];
    }

    function __call($method, $args){
        $this->props[$method] = $args[0];
        return $this;
    }

    function render(){
        $b = new (static::$builder)();

        $t = $b->{$this->name}(...$this->props);
        return $t->render(null);
    }

    function __toString()
    {
        return $this->render();
    }

}

