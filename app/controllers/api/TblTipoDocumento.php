<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblTipoDocumento extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'tbl_usuario'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
