<?php

use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Libs\Mail;
use Boctulus\Simplerest\Core\Libs\Paginator;


require_once __DIR__ . '/constants.php';

return [
	'app_url'   => Env::get('APP_URL'),
	'app_name'  => Env::get('APP_NAME'),
	'app_env'   => Env::get('APP_ENV'),
	'namespace' => 'Boctulus\Simplerest',	

	/*
		For a sub-foder in /var/www/html just set as
	 	
		base_url' => /folder'
	*/

	'base_url' => '',   

	/*
		Routers and FrontController

		Dejar todos en true produce mucho delay
	*/
	'web_router'       => true,
	'console_router'   => true,
	'front_controller' => true,

	/*
		FrontController Handlers (Behaviors)

		Configurable classes for handling different aspects of request processing.
		You can replace any handler with a custom implementation.
	*/
	'front_behaviors' => [
		'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
		'api'        => Boctulus\Simplerest\Core\Handlers\ApiHandler::class,
		'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
		'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
		'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
		'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
	],

	/*
		urls start with /api/ if remove_api_slug is set to false
	*/	

	'remove_api_slug' => false, 

	'default_controller' => 'HomeController',

	'template' => 'templates/tpl.php',

	/*
		true  | 1 | on 
		false | 0 | off
		null
	*/

	'https' =>  null, 

	/*
		ssl certificate file
		null
		false -> deshabilita la verificacion
	*/
	
	'ssl_cert'     => null,  // 'D:\wamp64\ca-bundle.crt',

	'DateTimeZone' => 'Asia/Manila',

	'tmp_dir'	   => sys_get_temp_dir(),

	/*
		Intercepta errores
	*/
	
	'error_handling' => false,

	/*
		Puede mostrar detalles como consultas SQL fallidas 

		Ver 'log_sql'
	*/

	'debug'   		 => true,  //Env::get('APP_DEBUG', true),

	/*
		Si error_log es true entonces se usara error_log()
		como logger() y sino sera implementacion propia
		del framework 
	*/

	'error_log'      =>  true,

	'log_file'       => 'log.txt',
	
	/*
		Loguea cada consulta / statement -al menos las ejecutadas usando Model-

		Solo aplica si 'debug' esta en true
	
	*/

	'log_sql'         => true,
	
	/*
		Genera logs por cada error / excepcion
	*/

	'log_errors'	 => true,

	/*
		Si se quiere incluir todo el trace del error -suele ser bastante largo-

		Solo aplica con 'log_errors' en true
	*/

	'log_stack_trace' => false,

	/*
		Define users's table name
	*/
	'users_table' =>  'users',

	/*
		Response format
	*/
	'include_enity_name' => false,
	'nest_sub_resources' => false,	

	'paginator' => [
		'max_limit' => 50,
		'default_limit' => 10,
		'position' => Paginator::TOP,
		'params'   => [
			'pageSize' => 'size',
			'page'	   => 'page' // usar 'page_num' para WordPress
		],
		'formatter' => function ($row_count, $count, $current_page, $page_count, $page_size, $nextUrl){
			return [
				"last_page" => $page_count,
				'paginator' => [
					"total"       => $row_count, 
					"count"       => $count,
					"currentPage" => $current_page,
					"totalPages"  => $page_count,
					"pageSize"    => $page_size,
					"nextUrl"	  => $nextUrl
				],
			];
		},
	],

	'pretty' => false,

	/*
		Restrictions can be aplied
	*/
	
	'restrict_by_ip'	     => false,
	'restrict_by_user_agent' => false,
	// solo deshabilitar en pruebas
	'restrict_by_tenant'     => false,     
	
	'acl_file' => SECURITY_PATH . 'acl.cache',

	'access_token' => [
		'secret_key' 		=> Env::get('TOKENS_ACCSS_SECRET_KEY'),
		'expiration_time'	=> 60 * 15 * 50000,   // seconds (normalmente 60 * 15)
		'encryption'		=> 'HS256'			
	],

	'refresh_token' => [
		'secret_key'		=> Env::get('TOKENS_REFSH_SECRET_KEY'),
		'expiration_time' 	=> 315360000,   // seconds
		'encryption' 		=> 'HS256'	
	],

	'email_token' => [
		'secret_key' => Env::get('TOKENS_EMAIL_SECRET_KEY'),
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
	'auto_approval_roles' => ['admin', 'usuario', 'supervisor', 'superadmin'],

	'default_role' => null,
	
	/*
		If you need email confirmation then pre_activated should be false
	*/
	
	'pre_activated' => true,

	'email' => [
		'from'		=> [
			'address' 		=> Env::get('MAIL_DEFAULT_FROM_ADDR'), 
			'name' 			=> Env::get('MAIL_DEFAULT_FROM_NAME')
		],	

		'mailers' => [
			'google' => [
				'Host'			=> Env::get('MAIL_HOST'),
				'Port'			=> Env::get('MAIL_PORT'),
				'Username' 		=> Env::get('MAIL_USERNAME'),
				'Password' 		=> Env::get('MAIL_PASSWORD'),
				'SMTPSecure'	=> Env::get('MAIL_ENCRYPTION'),
				'SMTPAuth' 		=> Env::get('MAIL_AUTH'),
				'SMTPDebug' 	=> 3,
				'CharSet' 		=> 'UTF-8',
				'Debugutput' 	=> 'html',

				// // Extras
				// 'SMTPOptions'   => [
				// 	'ssl' => [
				// 		'verify_peer' => false,
				// 		'verify_peer_name' => false,
				// 		'allow_self_signed' => true
				// 	]
				// ]
			],
		],

		'mailer_default'       => 'google',
		// 'mailer_class_default' =>  Mail::class
	],

	/*
		ApiClient default sleep time between requests
	*/

	'sleep_time' => 0.15,

	/*
		i18n
	*/

	'translate' => [
		'use_gettext' => false
	],

	/*
		Service Providers
	*/

	'providers' => [
		// Add your service providers here
		// Example: Boctulus\YourPackage\ServiceProvider::class,
	],


	/*
		Cache
	*/

	'cache_driver' => Boctulus\Simplerest\Core\Libs\DBCache::class,

	'var_dump_separators' => [
		'start' => '--| ',
		'end'   => ''
	],

	/*
		Si falta un paquete de Composer o el autoload.php o el composer.json intenta resolverlo

		Requiere que Composer este instalado
	*/

	'use_composer' => true,

	'openai_api_key'         => Env::get('OPENAI_API_KEY'),
	'claude_api_key'      	 => Env::get('CLAUDE_API_KEY'),
	
	'google_console_api_key' => Env::get('GOOGLE_CONSOLE_API_KEY'),
	'google_maps_api_key'    => 'AIzaSyAJI6R4DUNCfwvQYZJZGltf9qztLnQMzKY',
	
	'sendinblue_api_key' => Env::get('SENDINBLUE_API_KEY'),

	'google_auth'  => [
		'client_id' 	=> Env::get('OAUTH_GOOGLE_CLIENT_ID'),
		'client_secret' => Env::get('OAUTH_GOOGLE_CLIENT_SECRET'),
		'callback_url' 	=> Env::get('OAUTH_GOOGLE_CALLBACK')
	],

	'facebook_auth' => [
		'app_id' 		=> Env::get('OAUTH_FACEBOOK_CLIENT_ID'),
		'app_secret'	=> Env::get('OAUTH_FACEBOOK_CLIENT_SECRET'), 
		'callback_url'	=> Env::get('OAUTH_FACEBOOK_CALLBACK')
	],

];