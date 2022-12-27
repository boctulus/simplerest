<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EmailNotificationsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'email_notifications',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'from_addr', 'from_name', 'to_addr', 'to_name', 'cc_addr', 'cc_name', 'bcc_addr', 'bcc_name', 'replyto_addr', 'subject', 'body', 'sent_at', 'created_at', 'deleted_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'from_addr' => 'STR',
				'from_name' => 'STR',
				'to_addr' => 'STR',
				'to_name' => 'STR',
				'cc_addr' => 'STR',
				'cc_name' => 'STR',
				'bcc_addr' => 'STR',
				'bcc_name' => 'STR',
				'replyto_addr' => 'STR',
				'subject' => 'STR',
				'body' => 'STR',
				'sent_at' => 'STR',
				'created_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'from_addr', 'from_name', 'to_name', 'cc_addr', 'cc_name', 'bcc_addr', 'bcc_name', 'replyto_addr', 'body', 'sent_at', 'deleted_at'],

			'required'		=> ['to_addr', 'subject', 'created_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'from_addr' => ['type' => 'str', 'max' => 320],
				'from_name' => ['type' => 'str', 'max' => 80],
				'to_addr' => ['type' => 'str', 'max' => 320, 'required' => true],
				'to_name' => ['type' => 'str', 'max' => 80],
				'cc_addr' => ['type' => 'str', 'max' => 320],
				'cc_name' => ['type' => 'str', 'max' => 80],
				'bcc_addr' => ['type' => 'str', 'max' => 320],
				'bcc_name' => ['type' => 'str', 'max' => 80],
				'replyto_addr' => ['type' => 'str', 'max' => 320],
				'subject' => ['type' => 'str', 'max' => 80, 'required' => true],
				'body' => ['type' => 'str'],
				'sent_at' => ['type' => 'datetime'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'deleted_at' => ['type' => 'datetime']
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

