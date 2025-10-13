<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

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
