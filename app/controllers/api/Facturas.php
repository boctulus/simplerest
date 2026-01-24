<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

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
