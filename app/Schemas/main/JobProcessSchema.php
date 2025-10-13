<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class JobProcessSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'job_process',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'queue', 'job_id', 'pid', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'queue' => 'STR',
				'job_id' => 'INT',
				'pid' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'queue'],

			'required'			=> ['job_id', 'pid', 'created_at'],

			'uniques'			=> ['job_id', 'pid'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'queue' => ['type' => 'str', 'max' => 60],
				'job_id' => ['type' => 'int', 'required' => true],
				'pid' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 				=> [],

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

