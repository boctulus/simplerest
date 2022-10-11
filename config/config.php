<?php

use simplerest\core\Paginator;

require_once __DIR__ . '/constants.php';

// puede afectar el punto decimal al formar sentencias SQL !!!
// setlocale(LC_ALL, 'es_AR.UTF-8');

return [
	'app_url' => env('APP_URL'),

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

	/*
		Es posible cargar la lista de conexiones disponibles
		de forma dinámica
	*/

	'db_connections' => // get_db_connections()
	
	[
		'main' => [
			'host'		=> env('DB_HOST', '127.0.0.1'),
			'port'		=> env('DB_PORT'),
			'driver' 	=> env('DB_CONNECTION'),
			'db_name' 	=> env('DB_NAME'),
			'user'		=> env('DB_USERNAME'), 
			'pass'		=> env('DB_PASSWORD'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'az' => [
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

		'ef' => [
			'host'		=> env('DB_HOST_2', '127.0.0.1'),
			'port'		=> env('DB_PORT_2'),
			'driver' 	=> env('DB_CONNECTION_2'),
			'db_name' 	=> env('DB_NAME_2'),
			'user'		=> env('DB_USERNAME_2'), 
			'pass'		=> env('DB_PASSWORD_2'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'eb' => [
			'host'		=> env('DB_HOST_2', '127.0.0.1'),
			'port'		=> env('DB_PORT_2'),
			'driver' 	=> env('DB_CONNECTION_2'),
			'db_name' 	=> env('DB_NAME_2'),
			'user'		=> env('DB_USERNAME_2'), 
			'pass'		=> env('DB_PASSWORD_2'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'mpo' => [
			'host'		=> env('DB_HOST_MPO_DOCKER', '127.0.0.1'),
			'port'		=> env('DB_PORT_MPO_DOCKER'),
			'driver' 	=> env('DB_CONNECTION_MPO_DOCKER'),
			'db_name' 	=> env('DB_NAME_MPO_DOCKER'),
			'user'		=> env('DB_USERNAME_MPO_DOCKER'), 
			'pass'		=> env('DB_PASSWORD_MPO_DOCKER'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'mpp' => [
			'host'		=> '51.161.116.202', //env('DB_HOST_MPP'),
			'port'		=> env('DB_PORT_MPP'),
			'driver' 	=> env('DB_CONNECTION_MPP'),
			'db_name' 	=> env('DB_NAME_MPP'),
			'user'		=> env('DB_USERNAME_MPP'), 
			'pass'		=> env('DB_PASSWORD_MPP'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],
		
	], 	

	'db_connection_default' => 'main',

    'tentant_groups' => [
        // 'legion' => [
        //     'db_[0-9]+',
        //     'db_legion',
        //     'db_flor'
		// ],
		'az' => [
			'az'
		],
		'ef' => [
			'ef'
		],
		'eb' => [
			'eb'
		],
		'mpp' => [
			'mpp'
		],
		'mpo' => [
			'mpp'
		]
    ], 
	
	'DateTimeZone' => 'Europe/London',

	'error_handling'   => true,
	'debug'   => true,  //env('APP_DEBUG', true),

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
		'position' => Paginator::TOP
	],

	'pretty' => false,

	/*
		Restrictions can be aplied
	*/
	
	'restrict_by_ip'	=> false,
	'restrict_by_user_agent' => false,
	// solo deshabilitar en pruebas
	'restrict_by_tenant' => false,     
	
	'acl_file' => SECURITY_PATH . 'acl.cache',

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
	'auto_approval_roles' => ['admin', 'usuario', 'supervisor', 'superadmin'],

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


	/*
		Service Providers
	*/

	'providers' => [
		devdojo\calculator\CalculatorServiceProvider::class,
		boctulus\grained_acl\GrainedAclServiceProvider::class,
		//boctulus\basic_acl\BasicAclServiceProvider::class
		// ...
	],

	'var_dump_separators' => [
		'start' => '--| ',
		'end'   => ''
	],

	'google_maps_api_key' => 'AIzaSyAJI6R4DUNCfwvQYZJZGltf9qztLnQMzKY'
	
];