<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblContacto extends ApiController
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
