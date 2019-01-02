<?php

return [
		// DB
		'host' => 'localhost',
		'db_name' => 'api_sb', 
		'user' => 'root', 
		'pass' => '', 
		
		// JWT
		'jwt_secret_key' =>'',
		'token_expiration_time' => 5, // minutes
		'extended_token_expiration_time' => 3, // minutes
		'encryption' => 'HS256',
		'enabled_auth' => false
	];