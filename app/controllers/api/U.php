<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class U extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'u_settings'		
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
