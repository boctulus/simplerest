<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MessagesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'messages',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'from_email', 'from_name', 'to_email', 'to_name', 'subject', 'body', 'created_at', 'sent_at'],

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

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'to_name', 'sent_at'],

			'required'		=> ['from_email', 'from_name', 'to_email', 'subject', 'body', 'created_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'from_email' => ['type' => 'str', 'max' => 320, 'required' => true],
				'from_name' => ['type' => 'str', 'max' => 60, 'required' => true],
				'to_email' => ['type' => 'str', 'max' => 320, 'required' => true],
				'to_name' => ['type' => 'str', 'max' => 40],
				'subject' => ['type' => 'str', 'max' => 40, 'required' => true],
				'body' => ['type' => 'str', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'sent_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

