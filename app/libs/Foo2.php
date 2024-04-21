<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

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
