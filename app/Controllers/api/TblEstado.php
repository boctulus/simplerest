<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblEstado extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        "tbl_categoria_persona",
        'tbl_usuario'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
