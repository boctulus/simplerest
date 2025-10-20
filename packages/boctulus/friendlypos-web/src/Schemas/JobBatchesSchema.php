<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class JobBatchesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'job_batches',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'failed_job_ids', 'options', 'cancelled_at', 'created_at', 'finished_at'],

			'attr_types'		=> [
				'id' => 'STR',
				'name' => 'STR',
				'total_jobs' => 'INT',
				'pending_jobs' => 'INT',
				'failed_jobs' => 'INT',
				'failed_job_ids' => 'STR',
				'options' => 'STR',
				'cancelled_at' => 'INT',
				'created_at' => 'INT',
				'finished_at' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> null,

			'nullable'			=> ['options', 'cancelled_at', 'finished_at'],

			'required'			=> ['id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'failed_job_ids', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'str', 'max' => 255, 'required' => true],
				'name' => ['type' => 'str', 'max' => 255, 'required' => true],
				'total_jobs' => ['type' => 'int', 'required' => true],
				'pending_jobs' => ['type' => 'int', 'required' => true],
				'failed_jobs' => ['type' => 'int', 'required' => true],
				'failed_job_ids' => ['type' => 'str', 'required' => true],
				'options' => ['type' => 'str'],
				'cancelled_at' => ['type' => 'int'],
				'created_at' => ['type' => 'int', 'required' => true],
				'finished_at' => ['type' => 'int']
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

