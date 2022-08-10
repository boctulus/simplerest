<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProyectosEjecutadosRecurPublicosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'proyectos_ejecutados_recur_publicos',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'anno', 'duracion', 'valor', 'entidad', 'created_at', 'updated_at'],

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

			'required'		=> ['anno', 'duracion', 'valor', 'entidad'],

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

