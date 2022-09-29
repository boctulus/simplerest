<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class UserSpPermissions extends MyApiController
{    
	static protected $connect_to = [
		'sp_permissions'
	];
        
} // end class
