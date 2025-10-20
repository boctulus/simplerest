<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class EmpresaProductoPack extends MyApiController
{ 
    static protected $soft_delete = true;
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
