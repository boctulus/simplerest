<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblProducto extends ApiController
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
