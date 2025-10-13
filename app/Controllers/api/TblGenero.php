<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblGenero extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'tbl_usuario',
        'tbl_estado'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
