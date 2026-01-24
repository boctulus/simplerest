<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class Barrios extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    //public $model_name = 'BarriosModel';

    function __construct()
    {       
        parent::__construct();
    }        
} 
