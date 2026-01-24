<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Libs\Mail;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Controllers\MyApiController; 

class EmailNotifications extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    function __construct()
    {       
        parent::__construct();
    }    
    
    /*
		Si lo hago acá será síncrono (mala idea)
	*/
	// function onCreated(array &$data, $last_inserted_id)
	// {
	// 	// podría ser un CASE
	// 	if ($data['subject'] == 'Cambio de contraseña'){
	// 		$u_model_name = get_user_model_name();
	// 		$u = new $u_model_name();

	// 		$username = $u
	// 		->where([
	// 			$u_model_name::$email => $data['to_addr'] 
	// 		])
	// 		->value($u_model_name::$username);

	// 		if ($username == false){
	// 			$username = 'Usuario';
	// 			Logger::log("Username para {$data['to_addr']} es indefinido !");
	// 		}

	// 		Mail::send($data['to_addr'], $username, $data['subject'], $data['body']);
	// 	}
	// }
} 
