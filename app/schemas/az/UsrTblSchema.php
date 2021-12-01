<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsrTblSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'usr_tbl',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'nam_col' => 'STR',
				'age_col' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['nam_col', 'age_col'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'str', 'max' => 32, 'required' => true],
				'nam_col' => ['type' => 'str', 'max' => 32],
				'age_col' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				'job_tbl' => [
					['job_tbl.id','usr_tbl.id']
				]
			],

			'expanded_relationships' => array (
				  'job_tbl' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'job_tbl',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'usr_tbl',
				        1 => 'id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}

