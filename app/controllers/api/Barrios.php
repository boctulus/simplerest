<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class Barrios extends ApiController
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
