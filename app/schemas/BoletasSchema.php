<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BoletasSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'boletas',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'edad' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'username' => 'STR',
				'password' => 'STR',
				'password_char' => 'STR',
				'texto_vb' => 'STR',
				'texto' => 'STR',
				'texto_tiny' => 'STR',
				'texto_md' => 'STR',
				'texto_long' => 'STR',
				'codigo' => 'STR',
				'blob_tiny' => 'STR',
				'blob_md' => 'STR',
				'blob_long' => 'STR',
				'bb' => 'STR',
				'json_str' => 'STR',
				'karma' => 'INT',
				'code' => 'INT',
				'big_num' => 'INT',
				'ubig' => 'INT',
				'medium' => 'INT',
				'small' => 'INT',
				'tiny' => 'INT',
				'saldo' => 'STR',
				'flotante' => 'STR',
				'doble_p' => 'STR',
				'num_real' => 'STR',
				'some_bits' => 'BOOL',
				'is_active' => 'INT',
				'paused' => 'INT',
				'flavors' => 'STR',
				'role' => 'STR',
				'hora' => 'STR',
				'birth_year' => 'STR',
				'fecha' => 'STR',
				'vencimiento' => 'STR',
				'ts' => 'STR',
				'deleted_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'correo' => 'STR',
				'user_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['lastname', 'password_char', 'karma', 'is_active', 'paused', 'vencimiento', 'ts'],

			'uniques'		=> ['username', 'correo'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'edad' => ['type' => 'int', 'min' => 0, 'required' => true],
				'firstname' => ['type' => 'str', 'max' => 60, 'required' => true],
				'lastname' => ['type' => 'str', 'max' => 60],
				'username' => ['type' => 'str', 'max' => 60, 'required' => true],
				'password' => ['type' => 'str', 'max' => 128, 'required' => true],
				'password_char' => ['type' => 'str'],
				'texto_vb' => ['max' => 300, 'required' => true],
				'texto' => ['type' => 'str', 'required' => true],
				'texto_tiny' => ['type' => 'str', 'required' => true],
				'texto_md' => ['type' => 'str', 'required' => true],
				'texto_long' => ['type' => 'str', 'required' => true],
				'codigo' => ['type' => 'str', 'required' => true],
				'blob_tiny' => ['type' => 'str', 'required' => true],
				'blob_md' => ['type' => 'str', 'required' => true],
				'blob_long' => ['type' => 'str', 'required' => true],
				'bb' => ['type' => 'str', 'max' => 255, 'required' => true],
				'json_str' => ['type' => 'str', 'required' => true],
				'karma' => ['type' => 'int'],
				'code' => ['type' => 'int', 'min' => 0, 'required' => true],
				'big_num' => ['type' => 'int', 'required' => true],
				'ubig' => ['type' => 'int', 'min' => 0, 'required' => true],
				'medium' => ['type' => 'int', 'required' => true],
				'small' => ['type' => 'int', 'required' => true],
				'tiny' => ['type' => 'bool', 'required' => true],
				'saldo' => ['type' => 'decimal(15,4)', 'required' => true],
				'flotante' => ['type' => 'str', 'required' => true],
				'doble_p' => ['type' => 'double', 'required' => true],
				'num_real' => ['type' => 'double', 'required' => true],
				'some_bits' => ['type' => 'bool', 'required' => true],
				'is_active' => ['type' => 'bool'],
				'paused' => ['type' => 'bool'],
				'flavors' => ['type' => 'str', 'required' => true],
				'role' => ['type' => 'str', 'required' => true],
				'hora' => ['type' => 'time', 'required' => true],
				'birth_year' => ['type' => 'str', 'required' => true],
				'fecha' => ['type' => 'date', 'required' => true],
				'vencimiento' => ['type' => 'datetime'],
				'ts' => ['type' => 'timestamp'],
				'deleted_at' => ['type' => 'datetime', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime', 'required' => true],
				'correo' => ['type' => 'str', 'max' => 60, 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','boletas.user_id']
				]
			],

			'expanded_relationships' => array (
				  'users' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'boletas',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'users' => [
					['users.id','boletas.user_id']
				]
			],

			'expanded_relationships_from' => array (
				  'users' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'boletas',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}

