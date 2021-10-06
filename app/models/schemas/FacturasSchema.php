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
				'user_id' => 'INT',
				'sale_price_dec' => 'STR'
			],

			'nullable'		=> ['aaa', 'lastname', 'password_char', 'carma', 'active', 'vencimiento', 'ts', 'nuevo_campito', 'deleted_at'],

			'rules' 		=> [
				'aaa' => ['type' => 'str'],
				'id' => ['type' => 'int'],
				'edad' => ['type' => 'int', 'min' => 0],
				'firstname' => ['type' => 'str', 'max' => 60],
				'lastname' => ['type' => 'str', 'max' => 50],
				'username' => ['type' => 'str', 'max' => 50],
				'password' => ['type' => 'str', 'max' => 128],
				'password_char' => ['type' => 'str'],
				'texto_vb' => ['max' => 300],
				'texto' => ['type' => 'str'],
				'texto_tiny' => ['type' => 'str'],
				'texto_md' => ['type' => 'str'],
				'texto_long' => ['type' => 'str'],
				'codigo' => ['type' => 'str'],
				'blob_tiny' => ['type' => 'str'],
				'blob_md' => ['type' => 'str'],
				'blob_long' => ['type' => 'str'],
				'bb' => ['type' => 'str', 'max' => 255],
				'json_str' => ['type' => 'str'],
				'carma' => ['type' => 'int'],
				'code' => ['type' => 'int', 'min' => 0],
				'big_num' => ['type' => 'int'],
				'ubig' => ['type' => 'int', 'min' => 0],
				'medium' => ['type' => 'int'],
				'small' => ['type' => 'int'],
				'tiny' => ['type' => 'bool'],
				'flotante' => ['type' => 'str'],
				'doble_p' => ['type' => 'double'],
				'num_real' => ['type' => 'double'],
				'some_bits' => ['type' => 'bool'],
				'active' => ['type' => 'bool'],
				'flavors' => ['type' => 'str'],
				'role' => ['type' => 'str'],
				'hora' => ['type' => 'time'],
				'birth_year' => ['type' => 'int'],
				'fecha' => ['type' => 'date'],
				'vencimiento' => ['type' => 'int'],
				'ts' => ['type' => 'int'],
				'nuevo_campito' => ['type' => 'str', 'max' => 50],
				'deleted_at' => ['type' => 'datetime'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'correo' => ['type' => 'email', 'max' => 60],
				'user_id' => ['type' => 'int'],
				'sale_price_dec' => ['type' => 'decimal($nums)']
			],

			'relationships' => [
				'users' => [
					['users.id','facturas.user_id']
				]
			]
		];
	}	
}

