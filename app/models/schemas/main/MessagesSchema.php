<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MessagesSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
		return [
			'table_name'	=> 'messages',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR',
				'phone' => 'STR',
				'ip' => 'STR',
				'subject' => 'STR',
				'content' => 'STR',
				'sent_at' => 'STR',
				'created_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'nullable'		=> ['id', 'phone', 'ip', 'sent_at', 'deleted_at'],

			'rules' 		=> [
				'name' => ['max' => 80],
				'email' => ['max' => 320],
				'phone' => ['max' => 45],
				'subject' => ['max' => 40]
			],

			'relationships' => [
				
			]
		];
	}	
}

