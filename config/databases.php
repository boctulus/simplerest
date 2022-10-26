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

		'woo3' => [
			'host'		=> env('DB_HOST_WOO3', '127.0.0.1'),
			'port'		=> env('DB_PORT_WOO3'),
			'driver' 	=> env('DB_CONNECTION_WOO3'),
			'db_name' 	=> env('DB_NAME_WOO3'),
			'user'		=> env('DB_USERNAME_WOO3'), 
			'pass'		=> env('DB_PASSWORD_WOO3'),
			'charset'	=> env('DB_CHARSET_WOO3', 'utf8'),
			'schema'	=> null,  
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
];