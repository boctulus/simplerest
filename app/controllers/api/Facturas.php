<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class Facturas extends ApiController
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
