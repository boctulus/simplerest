<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class TblFactura extends MyApiController
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
