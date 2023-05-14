<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class AutomovilesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'automoviles',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'kilometraje', 'num_asientos', 'is_locked', 'created_at', 'updated_at', 'how_popular', 'quality'],

			'attr_types'	=> [
				'id' => 'INT',
				'kilometraje' => 'INT',
				'num_asientos' => 'INT',
				'is_locked' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'how_popular' => 'INT',
				'quality' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'kilometraje', 'is_locked', 'created_at', 'updated_at', 'how_popular', 'quality'],

			'required'		=> ['num_asientos'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'kilometraje' => ['type' => 'int'],
				'num_asientos' => ['type' => 'int', 'required' => true],
				'is_locked' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'how_popular' => ['type' => 'int'],
				'quality' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				'medios_transporte' => [
					['medios_transporte.automovil_id','automoviles.id']
				]
			],

			'expanded_relationships' => array (
  'medios_transporte' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'medios_transporte',
        1 => 'automovil_id',
      ),
      1 => 
      array (
        0 => 'automoviles',
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

