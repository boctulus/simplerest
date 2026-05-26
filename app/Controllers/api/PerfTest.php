<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class PerfTest extends ApiController
{ 
    static protected $soft_delete = false;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }        
} 
