<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SpPermissionsSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
		return [
			'table_name'	=> 'sp_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'name' => ['max' => 45]
			],

			'relationships' => [
				'user_sp_permissions' => [
					['user_sp_permissions.sp_permission_id','sp_permissions.id']
				]
			]
		];
	}	
}

