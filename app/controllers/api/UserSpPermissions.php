<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\core\libs\Validator;
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
