<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 
use Boctulus\Simplerest\Core\Libs\DB;

class TblEps extends ApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    function __construct()
    {       
        parent::__construct();       
    }    

}
    
 