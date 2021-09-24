<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class TblEstado extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        "tbl_categoria_persona"
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
