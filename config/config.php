<?php

require_once 'constants.php';

setlocale(LC_ALL, 'es_AR.UTF-8');

return [
	#
	# For a sub-foder in /var/www/html just set as
	# BASE_URL' => /folder/'
	#
	'BASE_URL' => '/',   

	'ROUTER' => true,
	'FRONT_CONTROLLER' => true,
	
	/*
		urls start with /api/ if REMOVE_API_SLUG is set to false
	*/	
	'REMOVE_API_SLUG' => false, 
	'HTTPS' => 'Off',
	'DEFAULT_CONTROLLER' => 'LoginController',

	'db_connections' => [
		'db1' => [
			'host'	=> 'localhost',
			'driver' => 'mysql',
			'db_name' => 'simplerest', 
			'user'	=> 'boctulus', 
			'pass'	=> 'gogogo#*$U&_441@#'
		],
		/*
		'db2' => [
			'host'	=> 'localhost',
			'driver' => 'mysql',
			'db_name' => 'simplerest', 
			'user'	=> 'boctulus', 
			'pass'	=> 'gogogo#*$U&_441@#'
		]
		*/
	], 

	'DateTimeZone' => 'America/Argentina/Buenos_Aires',

	'error_handling'   => true,
	'debug'   => true,

	'access_token' => [
		'secret_key' =>'',
		'expiration_time' => 60 * 15 * 10000,   // seconds (normalmente 60 * 15)
		'encryption' => 'HS256'			
	],

	'refresh_token' => [
		'secret_key' => '',
		'expiration_time' => 315360000,   // seconds
		'encryption' => 'HS256'	
	],

	'method_override' => [
		'by_url' => true,
		'by_header' => true
	],

	/* 
		Any role listed bellow if it is asked then will be auto-aproved.
	*/
	'auto_approval_roles' => ['basic', 'regular'],

	/*
		If you need email confirmation then pre_activated should be false
	*/
	'pre_activated' => false,

	// seconds
	'email' => [
		'secret_key' => '',
		'expires_in' => 7 * 24 * 3600,
		'encryption' => 'HS256',
		'mailer' =>  [	
			'from'	 => ['no_responder@simplerest.mapapulque.ro', 'No responder'],	
			'object' => [
				'Host' => 'smtp.easyname.com',
				'Username' => '162997mail6',
				'Password' => '',
				'Port' => 587,
	            'SMTPAuth' => true,
				'SMTPSecure' => 'ssl',
				'SMTPDebug' => 4,
				'CharSet' => 'UTF-8',
				'Debugoutput' => 'html',
				'SMTPSecure' => false
			]
		]
	],

	'pretty' => false,	
	
	'paginator' => [
					'max_limit' => 50,
					'default_limit' => 10
	],

	'google_auth'  => [
		'client_id' => '228180780767-4p8t6nvocukmu44ti57o60n1ck6sokpd.apps.googleusercontent.com',
		'client_secret' => '',
		// https://simplerest.mapapulque.ro/login/google_login
		'callback_url' => 'http://simplerest.co/login/google_login'
	],

	'facebook_auth' => [
		'app_id' => '533640957216135',
		'app_secret' => '', 
		'callback_url' => 'https://simplerest.mapapulque.ro/login/fb_login'
	]
	
];