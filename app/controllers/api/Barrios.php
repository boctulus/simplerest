<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

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
