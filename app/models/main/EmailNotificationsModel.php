<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\EmailNotificationsSchema;
use simplerest\libs\Mails;
use simplerest\libs\DB;
use simplerest\libs\Files;

class EmailNotificationsModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';	// 

    function __construct(bool $connect = false){
        parent::__construct($connect, EmailNotificationsSchema::class);
	}	

	/*
		Si lo hago acá será síncrono (mala idea)
	*/
	function onCreated(array &$data, $last_inserted_id)
	{
		// podría ser un CASE
		if ($data['subject'] == 'Cambio de contraseña'){
			$u_model_name = get_user_model_name();
			$u = new $u_model_name();

			$username = $u
			->where([
				$u_model_name::$email => $data['to_addr'] 
			])
			->value($u_model_name::$username);

			if ($username == false){
				$username = 'Usuario';
				Files::logger("Username para {$data['to_addr']} es indefinido !");
			}

			Mails::sendMail($data['to_addr'], $username, $data['subject'], $data['body']);
		}
	}
}
