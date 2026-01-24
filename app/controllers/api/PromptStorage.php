<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Controllers\MyApiController; 

class PromptStorage extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        DB::getConnection('main');        
        parent::__construct();
    }        
} 
