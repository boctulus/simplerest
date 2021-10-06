<?php

require_once 'constants.php';
require_once HELPERS_PATH. 'etc.php';
require_once HELPERS_PATH. 'db_dynamic_load.php'; // * !

// puede afectar el punto decimal al formar sentencias SQL !!!
// setlocale(LC_ALL, 'es_AR.UTF-8');

global $refresh_con;

if (!isset($refresh_conn)){
	$refresh_conn = true;
}

return [
	'APP_URL' => env('APP_URL'),

	#
	# For a sub-foder in /var/www/html just set as
	# BASE_URL' => /folder'
	#
	'BASE_URL' => '/',   

	'ROUTER' => true,
	'FRONT_CONTROLLER' => true,
	
	/*
		urls start with /api/ if REMOVE_API_SLUG is set to false
	*/	
	'REMOVE_API_SLUG' => false, 
	'HTTPS' => 'Off',
	'DEFAULT_CONTROLLER' => 'HomeController',

	/*
		Es posible cargar la lista de conexiones disponibles
		de forma dinámica
	*/

	'db_connections' => get_db_connections($refresh_conn)
	/*
	[
		'main' => [
			'host'		=> env('DB_HOST', '127.0.0.1'),
			'port'		=> env('DB_PORT'),
			'driver' 	=> env('DB_CONNECTION'),
			'db_name' 	=> env('DB_DATABASE'),
			'user'		=> env('DB_USERNAME'), 
			'pass'		=> env('DB_PASSWORD'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'test' => [
			'host'		=> env('DB_HOST', '127.0.0.1'),
			'port'		=> env('DB_PORT'),
			'driver' 	=> env('DB_CONNECTION'),
			'db_name' 	=> 'az',
			'user'		=> env('DB_USERNAME'), 
			'pass'		=> env('DB_PASSWORD'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'db_legion' => [
			'host'		=> env('DB_HOST_DSI', '127.0.0.1'),
			'port'		=> env('DB_PORT_DSI'),
			'driver' 	=> env('DB_CONNECTION_DSI'),
			'db_name' 	=> 'db_legion_20210809_Manana', 
			'user'		=> env('DB_USERNAME_DSI'), 
			'pass'		=> env('DB_PASSWORD_DSI'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],	

		'db_flor' => [
			'host'		=> env('DB_HOST_DSI', '127.0.0.1'),
			'port'		=> env('DB_PORT_DSI'),
			'driver' 	=> env('DB_CONNECTION_DSI'),
			'db_name' 	=> 'db_flor', 
			'user'		=> env('DB_USERNAME_DSI'), 
			'pass'		=> env('DB_PASSWORD_DSI'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],
		'db_utest' => [
			'host'		=> env('DB_HOST_DSI', '127.0.0.1'),
			'port'		=> env('DB_PORT_DSI'),
			'driver' 	=> env('DB_CONNECTION_DSI'),
			'db_name' 	=> 'db_utest', 
			'user'		=> env('DB_USERNAME_DSI'), 
			'pass'		=> env('DB_PASSWORD_DSI'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		]
	] */ , 	

	'db_connection_default' => 'main', 
	
	'DateTimeZone' => 'America/Bogota',

	'error_handling'   => false,
	'debug'   => env('APP_DEBUG', true),

	/*
		Define users's table name
	*/
	'users_table' =>  'tbl_usuario_empresa',

	/*
		Response format
	*/
	'include_enity_name' => true,
	'nest_sub_resources' => false,	
	'paginator' => [
					'max_limit' => 50,
					'default_limit' => 10,
					'position' => 'BOTTOM'
	],

	'pretty' => false,

	/*
		Restrictions can be aplied
	*/
	'restrict_by_ip'	=> true,
	'restrict_by_user_agent' => false,

	'access_token' => [
		'secret_key' 		=> env('TOKENS_ACCSS_SECRET_KEY'),
		'expiration_time'	=> 60 * 15 * 10000,   // seconds (normalmente 60 * 15)
		'encryption'		=> 'HS256'			
	],

	'refresh_token' => [
		'secret_key'		=> env('TOKENS_REFSH_SECRET_KEY'),
		'expiration_time' 	=> 315360000,   // seconds
		'encryption' 		=> 'HS256'	
	],

	'email_token' => [
		'secret_key' => env('TOKENS_EMAIL_SECRET_KEY'),
		'expires_in' => 7 * 24 * 3600,
		'encryption' => 'HS256'
	],

	'method_override' => [
		'by_url' => true,
		'by_header' => true
	],

	/* 
		Any role listed bellow if it is asked then will be auto-aproved.
	*/
	'auto_approval_roles' => ['admin', 'usuario', 'superadmin'],

	/*
		If you need email confirmation then pre_activated should be false
	*/
	'pre_activated' => true,

	'email' => [
		'from'		=> [
			'address' 		=> env('MAIL_DEFAULT_FROM_ADDR'), 
			'name' 			=> env('MAIL_DEFAULT_FROM_NAME')
		],	

		'mailers' => [
			'smtp' => [
				'Host'			=> env('MAIL_HOST'),
				'Port'			=> env('MAIL_PORT'),
				'Username' 		=> env('MAIL_USERNAME'),
				'Password' 		=> env('MAIL_PASSWORD'),
				'SMTPSecure'	=> env('MAIL_ENCRYPTION'),
				'SMTPAuth' 		=> env('MAIL_AUTH'),
				'SMTPDebug' 	=> 4,
				'CharSet' 		=> 'UTF-8',
				'Debugutput' 	=> 'html'
			]
		],

		'mailer_default' => 'smtp'
	],

	'google_auth'  => [
		'client_id' 	=> env('OAUTH_GOOGLE_CLIENT_ID'),
		'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET'),
		'callback_url' 	=> env('OAUTH_GOOGLE_CALLBACK')
	],

	'facebook_auth' => [
		'app_id' 		=> env('OAUTH_FACEBOOK_CLIENT_ID'),
		'app_secret'	=> env('OAUTH_FACEBOOK_CLIENT_SECRET'), 
		'callback_url'	=> env('OAUTH_FACEBOOK_CALLBACK')
	],

	/*
		Service Providers
	*/

	'providers' => [
		devdojo\calculator\CalculatorServiceProvider::class,
		boctulus\grained_acl\GrainedAclServiceProvider::class,
		//boctulus\basic_acl\BasicAclServiceProvider::class
		// ...
	],
	
];