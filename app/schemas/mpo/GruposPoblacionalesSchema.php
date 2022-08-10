<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class GruposPoblacionalesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'grupos_poblacionales',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'required'		=> ['nombre'],

			'uniques'		=> ['nombre'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 20, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'entidades_registrantes' => [
					['entidades_registrantes.grupo_poblacional_id','grupos_poblacionales.id']
				]
			],

			'expanded_relationships' => array (
  'entidades_registrantes' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'entidades_registrantes',
        1 => 'grupo_poblacional_id',
      ),
      1 => 
      array (
        0 => 'grupos_poblacionales',
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

