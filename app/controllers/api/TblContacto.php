<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class TblContacto extends MyApiController
{ 
    static protected $soft_delete = true;
	static protected $connect_to = [
		'tbl_cargo',
		'tbl_ciudad'
	];
    
    function __construct()
    {       
        parent::__construct();
    }        
} 
