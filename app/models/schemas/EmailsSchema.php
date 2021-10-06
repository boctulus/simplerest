<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EmailsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'emails',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'from_email' => 'STR',
				'from_name' => 'STR',
				'to_email' => 'STR',
				'to_name' => 'STR',
				'subject' => 'STR',
				'body' => 'STR',
				'created_at' => 'STR',
				'sent_at' => 'STR'
			],

			'nullable'		=> ['id', 'from_email', 'from_name', 'to_name', 'sent_at'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'from_email' => ['type' => 'str', 'max' => 320],
				'from_name' => ['type' => 'str', 'max' => 60],
				'to_email' => ['type' => 'str', 'max' => 320, 'required' => true],
				'to_name' => ['type' => 'str', 'max' => 40],
				'subject' => ['type' => 'str', 'max' => 40, 'required' => true],
				'body' => ['type' => 'str', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'sent_at' => ['type' => 'datetime']
			],

			'relationships' => [
				
			]
		];
	}	
}

