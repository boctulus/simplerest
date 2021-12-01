<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class WebhooksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'webhooks',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR',
				'entity' => 'STR',
				'op' => 'STR',
				'conditions' => 'STR',
				'callback' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR',
				'created_by' => 'INT',
				'updated_at' => 'STR',
				'updated_by' => 'INT',
				'deleted_at' => 'STR',
				'deleted_by' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'name', 'conditions', 'belongs_to', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 50],
				'entity' => ['type' => 'str', 'max' => 50, 'required' => true],
				'op' => ['type' => 'str', 'max' => 10, 'required' => true],
				'conditions' => ['type' => 'str', 'max' => 1024],
				'callback' => ['type' => 'str', 'max' => 255, 'required' => true],
				'belongs_to' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'created_by' => ['type' => 'int'],
				'updated_at' => ['type' => 'datetime'],
				'updated_by' => ['type' => 'int'],
				'deleted_at' => ['type' => 'datetime'],
				'deleted_by' => ['type' => 'int']
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

