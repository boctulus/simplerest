<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FacturasSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'facturas',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'aaa' => 'STR',
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
				'carma' => 'INT',
				'code' => 'INT',
				'big_num' => 'INT',
				'ubig' => 'INT',
				'medium' => 'INT',
				'small' => 'INT',
				'tiny' => 'INT',
				'flotante' => 'STR',
				'doble_p' => 'STR',
				'num_real' => 'STR',
				'some_bits' => 'BOOL',
				'active' => 'INT',
				'flavors' => 'STR',
				'role' => 'STR',
				'hora' => 'STR',
				'birth_year' => 'INT',
				'fecha' => 'STR',
				'vencimiento' => 'INT',
				'ts' => 'INT',
				'nuevo_campito' => 'STR',
				'deleted_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'correo' => 'STR',
				'user_id' => 'INT'
			],

			'nullable'		=> ['aaa', 'lastname', 'password_char', 'carma', 'active', 'vencimiento', 'ts', 'nuevo_campito', 'deleted_at'],

			'rules' 		=> [
				'firstname' => ['max' => 60],
				'lastname' => ['max' => 50],
				'username' => ['max' => 50],
				'password' => ['max' => 128],
				'nuevo_campito' => ['max' => 50],
				'correo' => ['max' => 60]
			],

			'relationships' => [
				'users' => [
					['users.id','facturas.user_id']
				]
			]
		];
	}	
}

