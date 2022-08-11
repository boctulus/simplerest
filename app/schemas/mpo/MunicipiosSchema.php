<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MunicipiosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'municipios',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre', 'deleted_at', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'deleted_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'deleted_at', 'created_at', 'updated_at'],

			'required'		=> ['nombre'],

			'uniques'		=> ['nombre'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 60, 'required' => true],
				'deleted_at' => ['type' => 'timestamp'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'representantes_legales' => [
					['representantes_legales.municipio_exp_id','municipios.id']
				],
				'org_comunales' => [
					['org_comunales.municipio_id','municipios.id']
				]
			],

			'expanded_relationships' => array (
  'representantes_legales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'representantes_legales',
        1 => 'municipio_exp_id',
      ),
      1 => 
      array (
        0 => 'municipios',
        1 => 'id',
      ),
    ),
  ),
  'org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunales',
        1 => 'municipio_id',
      ),
      1 => 
      array (
        0 => 'municipios',
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

