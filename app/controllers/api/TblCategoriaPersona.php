<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class TblCategoriaPersona extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'tbl_estado'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
