<?php

return 
[
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
			'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',  // not-implemented
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'zippy' => [
			'host'		=> env('DB_HOST', '127.0.0.1'),
			'port'		=> env('DB_PORT'),
			'driver' 	=> env('DB_CONNECTION'),
			'db_name' 	=> 'zippy',
			'user'		=> env('DB_USERNAME'), 
			'pass'		=> env('DB_PASSWORD'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',  // not-implemented
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		// POS WEB realizado por Sr. Jorge en Laravel sobre la DB `appfrien_pventas`
		'laravel_pos' => [
			'host'		=> env('DB_HOST', '127.0.0.1'),
			'port'		=> env('DB_PORT'),
			'driver' 	=> env('DB_CONNECTION'),
			'db_name' 	=> 'appfrien_pventas',
			'user'		=> env('DB_USERNAME'), 
			'pass'		=> env('DB_PASSWORD'),
			'charset'	=> 'utf8',
			//'schema'	=> 'az',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'relmotor' => [
			'host'		=> env('DB_HOST_RELMOTOR', '127.0.0.1'),
			'port'		=> env('DB_PORT_RELMOTOR'),
			'driver' 	=> env('DB_CONNECTION_RELMOTOR'),
			'db_name' 	=> env('DB_NAME_RELMOTOR'),
			'user'		=> env('DB_USERNAME_RELMOTOR'), 
			'pass'		=> env('DB_PASSWORD_RELMOTOR'),
			'charset'	=> env('DB_CHARSET_RELMOTOR', 'utf8'),
			'schema'	=> null,  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => true // *
			],
			'tb_prefix'  => 'wp_',
		],

		'laravelshopify' => [
			'host'		=> env('DB_HOST', '127.0.0.1'),
			'port'		=> env('DB_PORT'),
			'driver' 	=> env('DB_CONNECTION'),
			'db_name' 	=> 'laravelshopify',
			'user'		=> env('DB_USERNAME'), 
			'pass'		=> env('DB_PASSWORD'),
			'charset'	=> 'utf8',
			//'schema'	=> '',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		'edu' => [
			'host'		=> env('DB_HOST_EDU', '127.0.0.1'),
			'port'		=> env('DB_PORT_EDU'),
			'driver' 	=> env('DB_CONNECTION_EDU'),
			'db_name' 	=> env('DB_NAME_EDU'),
			'user'		=> env('DB_USERNAME_EDU'), 
			'pass'		=> env('DB_PASSWORD_EDU'),
			'charset'	=> 'utf8',
			//'schema'	=> 'edu',  
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		],

		// 'woo3' => [
		// 	'host'		=> env('DB_HOST_WOO3', '127.0.0.1'),
		// 	'port'		=> env('DB_PORT_WOO3'),
		// 	'driver' 	=> env('DB_CONNECTION_WOO3'),
		// 	'db_name' 	=> env('DB_NAME_WOO3'),
		// 	'user'		=> env('DB_USERNAME_WOO3'), 
		// 	'pass'		=> env('DB_PASSWORD_WOO3'),
		// 	'charset'	=> env('DB_CHARSET_WOO3', 'utf8'),
		// 	'schema'	=> null,  
		// 	'pdo_options' => [
		// 		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		// 		\PDO::ATTR_EMULATE_PREPARES => true // *
		// 	],
		// 	'tb_prefix'  => 'wp_',
		// ],

		// 'parts' => [
		// 	'host'		=> env('DB_HOST_PARTS', '127.0.0.1'),
		// 	'port'		=> env('DB_PORT_PARTS', 3306),
		// 	'driver' 	=> env('DB_CONNECTION_PARTS', 'mysql'),
		// 	'db_name' 	=> env('DB_NAME_PARTS'),
		// 	'user'		=> env('DB_USERNAME_PARTS'), 
		// 	'pass'		=> env('DB_PASSWORD_PARTS'),
		// 	'charset'	=> env('DB_CHARSET_PARTS', 'utf8'),
		// 	'schema'	=> null,  
		// 	'pdo_options' => [
		// 		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		// 		\PDO::ATTR_EMULATE_PREPARES => true // *
		// 	],
		// 	'tb_prefix'  => 'xx_',
		// ],

		// 'parts-remote' => [
		// 	'host'		=> env('DB_HOST_PARTS', '167.99.226.45'),
		// 	'port'		=> env('DB_PORT_PARTS', 3306),
		// 	'driver' 	=> env('DB_CONNECTION_PARTS', 'mysql'),
		// 	'db_name' 	=> 'nhcmrxnpdy',
		// 	'user'		=> 'nhcmrxnpdy', 
		// 	'pass'		=> 'MQzSGjm39Q',
		// 	'charset'	=> env('DB_CHARSET_PARTS', 'utf8'),
		// 	'schema'	=> null,  
		// 	'pdo_options' => [
		// 		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		// 		\PDO::ATTR_EMULATE_PREPARES => true // *
		// 	],
		// 	'tb_prefix'  => '',
		// ],

		// 'eb' => [
		// 	'host'		=> env('DB_HOST_2', '127.0.0.1'),
		// 	'port'		=> env('DB_PORT_2'),
		// 	'driver' 	=> env('DB_CONNECTION_2'),
		// 	'db_name' 	=> env('DB_NAME_2'),
		// 	'user'		=> env('DB_USERNAME_2'), 
		// 	'pass'		=> env('DB_PASSWORD_2'),
		// 	'charset'	=> 'utf8',
		// 	//'schema'	=> 'az',  
		// 	'pdo_options' => [
		// 		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		// 		\PDO::ATTR_EMULATE_PREPARES => false
		// 	]
		// ],

		// 'mpo' => [
		// 	'host'		=> env('DB_HOST_MPO_DOCKER', '127.0.0.1'),
		// 	'port'		=> env('DB_PORT_MPO_DOCKER'),
		// 	'driver' 	=> env('DB_CONNECTION_MPO_DOCKER'),
		// 	'db_name' 	=> env('DB_NAME_MPO_DOCKER'),
		// 	'user'		=> env('DB_USERNAME_MPO_DOCKER'), 
		// 	'pass'		=> env('DB_PASSWORD_MPO_DOCKER'),
		// 	'charset'	=> 'utf8',
		// 	//'schema'	=> 'az',  
		// 	'pdo_options' => [
		// 		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		// 		\PDO::ATTR_EMULATE_PREPARES => false
		// 	]
		// ],

		// 'mpp' => [
		// 	'host'		=> '51.161.116.202', //env('DB_HOST_MPP'),
		// 	'port'		=> env('DB_PORT_MPP'),
		// 	'driver' 	=> env('DB_CONNECTION_MPP'),
		// 	'db_name' 	=> env('DB_NAME_MPP'),
		// 	'user'		=> env('DB_USERNAME_MPP'), 
		// 	'pass'		=> env('DB_PASSWORD_MPP'),
		// 	'charset'	=> 'utf8',
		// 	//'schema'	=> 'az',  
		// 	'pdo_options' => [
		// 		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		// 		\PDO::ATTR_EMULATE_PREPARES => false
		// 	]
		// ],

		'test_sqlite' => [
			'driver' => 'sqlite',
			'db_name' => ':memory:', // o STORAGE_PATH . 'test.sqlite'
			'host' => null,
			'user' => null,
			'pass' => null,
			'pdo_options' => null,
		],
		
	], 	

	'db_connection_default' => 'main',  

    'tentant_groups' => [
		// 'companies' => [
		// 	'company_db-[0-9]+',			
		// 	'company_testing'
		// ],

        // 'legion' => [
        //     'db_[0-9]+',
        //     'db_legion',
        //     'db_flor'
		// ],
    ], 
];