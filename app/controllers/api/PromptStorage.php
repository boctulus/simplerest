<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\controllers\MyApiController; 

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
