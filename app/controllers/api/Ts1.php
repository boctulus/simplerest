<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Ts1 extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
