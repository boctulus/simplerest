<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class __NAME__ extends MyApiController
{ 
    static protected $soft_delete = __SOFT_DELETE__;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }        
} 
