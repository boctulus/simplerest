<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EmailNotificationsSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
		return [
			'table_name'	=> 'email_notifications',

			'id_name'		=> 'id',

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

			'nullable'		=> ['id', 'from_addr', 'from_name', 'to_name', 'cc_addr', 'cc_name', 'bcc_addr', 'bcc_name', 'replyto_addr', 'body', 'sent_at', 'deleted_at'],

			'rules' 		=> [
				'from_addr' => ['max' => 320],
				'from_name' => ['max' => 80],
				'to_addr' => ['max' => 320],
				'to_name' => ['max' => 80],
				'cc_addr' => ['max' => 320],
				'cc_name' => ['max' => 80],
				'bcc_addr' => ['max' => 320],
				'bcc_name' => ['max' => 80],
				'replyto_addr' => ['max' => 320],
				'subject' => ['max' => 80]
			],

			'relationships' => [
				
			]
		];
	}	
}

