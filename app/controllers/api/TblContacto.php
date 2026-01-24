<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblContacto extends MyApiController
{ 
    static protected $soft_delete = true;
	static protected $connect_to = [
		'tbl_cargo',
		'tbl_ciudad',
        'tbl_empresa',
        'tbl_pais'
	];
    
    function __construct()
    {       
        parent::__construct();
    }        
} 
