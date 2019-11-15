<?php

require_once 'constants.php';

return [
	#
	# For a sub-foder in /var/www/html just set as
	# BASE_URL' => /folder/'
	#
	'BASE_URL' => '/',   
	'HTTPS' => 'Off',
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
		'expiration_time' => 600000000,   // seconds (normally 60 seconds)
		'encryption' => 'HS256'			
	],

	'refresh_token' => [
		'secret_key' => '',
		'expiration_time' => 315360000,   // seconds
		'encryption' => 'HS256'	
	],

	// aditional role to 'registered' after being registered
	// leave empty for none
	'registration_role' => 'regular',

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

	'pretty' => true,	
	
	// absolute LIMIT for queries
	'max_records' => 50,

	'google_auth'  => [
		'client_id' => '228180780767-4p8t6nvocukmu44ti57o60n1ck6sokpd.apps.googleusercontent.com',
		'client_secret' => '',
		// https://simplerest.mapapulque.ro/login/google_login
		'callback' => 'http://simplerest.co/login/google_login'
	],

	'facebook_auth' => [
		'app_id' => '533640957216135',
		'app_secret' => '', 
		'callback' => 'https://simplerest.mapapulque.ro/login/fb_login'
	]
	
];