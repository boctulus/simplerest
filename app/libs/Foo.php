<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Factory;

class Foo
{
    function __construct() { 
        d("Instanciando " . __CLASS__);
    }

    function bar(){
        d(rand(5000,9999));
    }

    function other(){
        d('bla bla');
    }
}

