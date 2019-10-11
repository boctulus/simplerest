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

	'debug_mode'   => true,
	
	'enabled_auth' => true,

	'access_token' => [
		'secret_key' =>'',
		'expiration_time' => 60000,   // seconds
		'encryption' => 'HS256'			
	],

	'refresh_token' => [
		'secret_key' => ''
	],

	'session' => [
		'secret_key' => ''
	],

	// podría haber otro límite que dependa del rol del usuario o algo en su registro
	'max_records' => 50
		
];