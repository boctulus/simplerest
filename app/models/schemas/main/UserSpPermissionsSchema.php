<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserSpPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'user_sp_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'sp_permission_id' => 'INT',
				'user_id' => 'INT',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_by' => 'INT',
				'updated_at' => 'STR'
			],

			'nullable'		=> ['id', 'created_by', 'updated_by', 'updated_at'],

			'rules' 		=> [

			],

			'relationships' => [
				'sp_permissions' => [
					['sp_permissions.id','user_sp_permissions.sp_permission_id']
				],
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','user_sp_permissions.user_id']
				]
			]
		];
	}	
}

