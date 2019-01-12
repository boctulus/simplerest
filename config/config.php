<?php
declare(strict_types=1);

require_once 'constants.php';

return [
		'BASE_URL' => 'SimpleRestFul/',
		'DEFAULT_CONTROLLER' => 'ProductsController',

		'database' => [
			'host' => 'localhost',
			'db_name' => 'api_sb', 
			'user' => 'root', 
			'pass' => ''
		], 
		
		// JWT
		'jwt_secret_key' =>'',
		'token_expiration_time' => 5, // minutes, i.e 5
		'extended_token_expiration_time' => 3, // minutes, i.e 3
		'encryption' => 'HS256',
		'enabled_auth' => true
	];