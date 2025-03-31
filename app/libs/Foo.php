<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Foo
{
    protected $value;

    function __construct() { 
        dd("Instanciando " . __CLASS__);

        $this->value = rand(5000,9999);
    }

    function bar(){
        dd($this->value, 'value');
    }

    function other(){
        dd('bla bla');
    }
}
