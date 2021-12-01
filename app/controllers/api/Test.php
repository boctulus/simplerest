<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Test extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    function __construct()
    {       
        parent::__construct();
    }      
    
    function get($id = null){
        return ['data' => 'Esto es una prueba'];
    }
} 
