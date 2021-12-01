<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ApiKeysSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'api_keys',

			'id_name'		=> 'uuid',

			'attr_types'	=> [
				'uuid' => 'STR',
				'value' => 'STR',
				'user_id' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['uuid'],

			'autoincrement' => null,

			'nullable'		=> ['created_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36, 'required' => true],
				'value' => ['type' => 'str', 'max' => 60, 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['user_id'],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','api_keys.user_id']
				]
			],

			'expanded_relationships' => array (
				  'tbl_usuario_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				      1 => 
				      array (
				        0 => 'api_keys',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','api_keys.user_id']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_usuario_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				      1 => 
				      array (
				        0 => 'api_keys',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}

