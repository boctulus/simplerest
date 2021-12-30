<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

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
