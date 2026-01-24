<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class Foo extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
