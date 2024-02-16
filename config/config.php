<?php

use simplerest\core\libs\Paginator;

require_once __DIR__ . '/constants.php';

return [
	'app_url' => env('APP_URL'),
	'app_name'=> env('APP_NAME'),
	'app_env' => env('APP_ENV'),

	/*
		For a sub-foder in /var/www/html just set as
	 	
		base_url' => /folder'
	*/

	'base_url' => '',   

	'router' => true,
	'front_controller' => true,
	
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
	
	'ssl_cert' => null,  // 'D:\wamp64\ca-bundle.crt',

	'DateTimeZone' => 'Asia/Manila',

	/*
		Intercepta errores
	*/
	
	'error_handling' => true,

	/*
		Puede mostrar detalles como consultas SQL fallidas 

		Ver 'log_sql'
	*/

	'debug'   		 => true,  //env('APP_DEBUG', true),

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
	'include_enity_name' => true,
	'nest_sub_resources' => false,	

	'paginator' => [
		'max_limit' => 50,
		'default_limit' => 10,
		'position' => Paginator::TOP,
		'params'   => [
			'pageSize' => 'size',
			'page'	   => 'page_num' // redefinido para WordPress
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
		'secret_key' 		=> env('TOKENS_ACCSS_SECRET_KEY'),
		'expiration_time'	=> 60 * 15 * 50000,   // seconds (normalmente 60 * 15)
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
	'auto_approval_roles' => ['admin', 'usuario', 'supervisor', 'superadmin'],

	'default_role' => null,
	
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
			'google' => [
				'Host'			=> env('MAIL_HOST'),
				'Port'			=> env('MAIL_PORT'),
				'Username' 		=> env('MAIL_USERNAME'),
				'Password' 		=> env('MAIL_PASSWORD'),
				'SMTPSecure'	=> env('MAIL_ENCRYPTION'),
				'SMTPAuth' 		=> env('MAIL_AUTH'),
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

			'pulque' => [
				'Host'			=> env('MAIL_HOST_3'),
				'Port'			=> env('MAIL_PORT_3'),
				'Username' 		=> env('MAIL_USERNAME_3'),
				'Password' 		=> env('MAIL_PASSWORD_3'),
				'SMTPSecure'	=> env('MAIL_ENCRYPTION_3'),
				'SMTPAuth' 		=> env('MAIL_AUTH_3'),
				//'SMTPAutoTLS'   => false,
				'SMTPDebug' 	=> 0,
				'CharSet' 		=> 'UTF-8',
				'Debugutput' 	=> 'html',

				// Extras
				'SMTPOptions'   => [
					'ssl' => [
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
					]
				]
			],

			'miguel_peru' => [
				'Host'			=> env('MAIL_HOST_4'),
				'Port'			=> env('MAIL_PORT_4'),
				'Username' 		=> env('MAIL_USERNAME_4'),
				'Password' 		=> env('MAIL_PASSWORD_4'),
				'SMTPSecure'	=> env('MAIL_ENCRYPTION_4'),
				'SMTPAuth' 		=> env('MAIL_AUTH_4'),
				'SMTPDebug' 	=> 0,
				'CharSet' 		=> 'UTF-8',
				'Debugutput' 	=> 'html',

				//Extras
				// 'SMTPOptions'   => [
				// 	'ssl' => [
				// 		'verify_peer' => false,
				// 		'verify_peer_name' => false,
				// 		'allow_self_signed' => true
				// 	]					
				// ]
			],

			'solbin_sblue' => [
				'Host'			=> env('MAIL_HOST_5'),
				'Port'			=> env('MAIL_PORT_5'),
				'Username' 		=> env('MAIL_USERNAME_5'),
				'Password' 		=> env('MAIL_PASSWORD_5'),
				'SMTPSecure'	=> env('MAIL_ENCRYPTION_5'),
				'SMTPAuth' 		=> env('MAIL_AUTH_5'),
				'SMTPDebug' 	=> 0,
				'CharSet' 		=> 'UTF-8',
				'Debugutput' 	=> 'html',

				//Extras
				// 'SMTPOptions'   => [
				// 	'ssl' => [
				// 		'verify_peer' => false,
				// 		'verify_peer_name' => false,
				// 		'allow_self_signed' => true
				// 	]					
				// ]
			]

		],

		'mailer_default' => 'google'
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
		devdojo\calculator\CalculatorServiceProvider::class,
		boctulus\grained_acl\GrainedAclServiceProvider::class,
		//boctulus\basic_acl\BasicAclServiceProvider::class
		// ...
		//boctulus\hello_world\HelloServiceProvider::class
	],

	
	/*
		Cache
	*/

	'cache_driver' => simplerest\core\libs\DBCache::class,


	'var_dump_separators' => [
		'start' => '--| ',
		'end'   => ''
	],


	/*
		Si falta un paquete de Composer o el autoload.php o el composer.json intenta resolverlo

		Requiere que Composer este instalado
	*/

	'use_composer' => true,

	'openai_api_key'         => env('OPENAI_API_KEY'),
	
	'google_console_api_key' => env('GOOGLE_CONSOLE_API_KEY'),
	'google_maps_api_key'    => 'AIzaSyAJI6R4DUNCfwvQYZJZGltf9qztLnQMzKY',
	
	'sendinblue_api_key' => env('SENDINBLUE_API_KEY'),

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

	'namespace' => 'simplerest'		
];