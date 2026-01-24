<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblPersona extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		"tbl_estado",
        "tbl_usuario",
        "tbl_tipo_persona",
        "tbl_pais",
        "tbl_ciudad",
        "tbl_genero",
        //"tbl_tipo_documento",
        "tbl_categoria_persona" 
	];

    function __construct()
    {       
        parent::__construct();
    }    
        
} 
