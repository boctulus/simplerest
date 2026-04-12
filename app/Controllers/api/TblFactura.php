<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblFactura extends ApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'tbl_consecutivo',
        'tbl_factura_detalle'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
