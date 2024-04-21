<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

class Foo
{
    function __construct() { 
        dd("Instanciando " . __CLASS__);
    }

    function bar(){
        dd(rand(5000,9999));
    }

    function other(){
        dd('bla bla');
    }
}
