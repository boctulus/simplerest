<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\libs\Debug;

class UserSpPermissions extends MyApiController
{    
	static protected $connect_to = [
		'sp_permissions'
	];

    function __construct()
    {
        parent::__construct();
    }

        
} // end class
