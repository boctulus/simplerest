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

		'API' => [
			'version'=> '1'
		],	
		
		// JWT
		'jwt_secret_key' =>'',
		'token_expiration_time' => 5, // minutes, i.e 5
		'extended_token_expiration_time' => 3, // minutes, i.e 3
		'encryption' => 'HS256',
		'enabled_auth' => true
	];