<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OrgVinculPersonalEntidadSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'org_vincul_personal_entidad',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'tipo_vinculo' => 'STR',
				'cant_personas' => 'INT',
				'entidad_registrante_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'tipo_vinculo' => ['type' => 'str', 'max' => 255, 'required' => true],
				'cant_personas' => ['type' => 'int', 'required' => true],
				'entidad_registrante_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

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

