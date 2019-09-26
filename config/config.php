<?php

require_once 'constants.php';

return [
		'BASE_URL' => '/',   
		'DEFAULT_CONTROLLER' => 'ProductsController',

		'database' => [
			'host' => 'localhost',
			'db_name' => 'api_sb', 
			'user' => 'boctulus', 
			'pass' => 'gogogo2k'
		], 

		'debug_mode' => true,
		
		// JWT
		'jwt_secret_key' =>'',
		'token_expiration_time' => 300,
		'encryption' => 'HS256',
		'enabled_auth' => true,

		// Refresh token
		'refresh_secret_key' => ''
	];