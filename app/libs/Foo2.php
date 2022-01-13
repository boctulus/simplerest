<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class Foo2 {
    public function __construct(
    		Bar $test, 
    		&$otro_param, 
    		$word = "Hello World", 
    		$options = array('a', 'b')
    	) {
    }
}

class Bar {
    public function __construct() {
    }
}

