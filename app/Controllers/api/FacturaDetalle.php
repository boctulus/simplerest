<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class FacturaDetalle extends ApiController
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
