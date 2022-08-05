<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProyectosRecurPropiosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'proyectos_recur_propios',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'anno' => 'INT',
				'duracion' => 'STR',
				'valor' => 'INT',
				'entidad' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'anno' => ['type' => 'bool', 'required' => true],
				'duracion' => ['type' => 'str', 'max' => 30, 'required' => true],
				'valor' => ['type' => 'int', 'required' => true],
				'entidad' => ['type' => 'str', 'max' => 40, 'required' => true],
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

