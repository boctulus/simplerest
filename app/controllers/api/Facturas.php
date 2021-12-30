<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Facturas extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'factura_detalle'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
