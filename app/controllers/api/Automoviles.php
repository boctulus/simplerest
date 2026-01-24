<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class Automoviles extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    // No es lo mismo $hidden en el modelo
    static protected $hidden   = [
		"created_at",
        "updated_at",
        "belongs_to",
        "is_locked"
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
