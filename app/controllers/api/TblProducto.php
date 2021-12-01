<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class TblProducto extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        'tbl_unidadmedida',
		'tbl_usuario'        
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
