<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

// API de tabla puente
class TblCategoriaPersonaPersona extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        'tbl_categoria_persona',
        'tbl_persona'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
