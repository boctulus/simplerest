<?php

require_once 'constants.php';

return [
	'BASE_URL' => '/',   
	'DEFAULT_CONTROLLER' => 'ProductsController',

	'database' => [
		'host' => 'localhost',
		'db_name' => 'simplerest', 
		'user' => 'simplerest', 
		'pass' => 'Hvr0tf9Is'
	], 

	'debug_mode'   => true,
	
	'enabled_auth' => true,

	'access_token' => [
		'secret_key' =>'',
		'expiration_time' => 60000,   // seconds
		'encryption' => 'HS256'			
	],

	'refresh_token' => [
		'secret_key' => '',
		'expiration_time' => 315360000,   // seconds
		'encryption' => 'HS256'	
	],

	// 'registered' or other
	'registration_role' => 'regular',

	// seconds
	'email' => [
		'secret_key' => '',
		'expires_in' => 7 * 24 * 3600,
		'encryption' => 'HS256'	
	],

	'pretty' => true,	
	
	// absolute LIMIT for queries
	'max_records' => 50,

	'google_auth'  => [
		'client_id' => '228180780767-4p8t6nvocukmu44ti57o60n1ck6sokpd.apps.googleusercontent.com',
		'client_secret' => '',
		'callback' => 'https://simplerest.mapapulque.ro/login/google_login'
	],

	'facebook_auth' => [
		'app_id' => '533640957216135',
		'app_secret' => '', 
		'callback' => 'https://simplerest.mapapulque.ro/login/fb_login'
	]
	
];