<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class FailedJobsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'failed_jobs',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'uuid' => 'STR',
				'connection' => 'STR',
				'queue' => 'STR',
				'payload' => 'STR',
				'exception' => 'STR',
				'failed_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'failed_at'],

			'required'			=> ['uuid', 'connection', 'queue', 'payload', 'exception'],

			'uniques'			=> ['uuid'],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'uuid' => ['type' => 'str', 'max' => 255, 'required' => true],
				'connection' => ['type' => 'str', 'required' => true],
				'queue' => ['type' => 'str', 'required' => true],
				'payload' => ['type' => 'str', 'required' => true],
				'exception' => ['type' => 'str', 'required' => true],
				'failed_at' => ['type' => 'timestamp']
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

