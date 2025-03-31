<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class FacturaDetalle extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'products'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
