<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class JobTblSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'job_tbl',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'function' => 'STR',
				'title' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['function', 'title'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'str', 'max' => 32, 'required' => true],
				'function' => ['type' => 'str', 'max' => 32],
				'title' => ['type' => 'str', 'max' => 32]
			],

			'fks' 			=> ['id'],

			'relationships' => [
				'usr_tbl' => [
					['usr_tbl.id','job_tbl.id']
				]
			],

			'expanded_relationships' => array (
				  'usr_tbl' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'usr_tbl',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'job_tbl',
				        1 => 'id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'usr_tbl' => [
					['usr_tbl.id','job_tbl.id']
				]
			],

			'expanded_relationships_from' => array (
				  'usr_tbl' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'usr_tbl',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'job_tbl',
				        1 => 'id',
				      ),
				    ),
				  ),
				)
		];
	}	
}

