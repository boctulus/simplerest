<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MediosTransporteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'medios_transporte',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'marca', 'modelo', 'automovil_id'],

			'attr_types'	=> [
				'id' => 'INT',
				'marca' => 'STR',
				'modelo' => 'INT',
				'automovil_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'automovil_id'],

			'required'		=> ['marca', 'modelo'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'marca' => ['type' => 'str', 'max' => 50, 'required' => true],
				'modelo' => ['type' => 'int', 'required' => true],
				'automovil_id' => ['type' => 'int']
			],

			'fks' 			=> ['automovil_id'],

			'relationships' => [
				'automoviles' => [
					['automoviles.id','medios_transporte.automovil_id']
				]
			],

			'expanded_relationships' => array (
  'automoviles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'automoviles',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'medios_transporte',
        1 => 'automovil_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'automoviles' => [
					['automoviles.id','medios_transporte.automovil_id']
				]
			],

			'expanded_relationships_from' => array (
  'automoviles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'automoviles',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'medios_transporte',
        1 => 'automovil_id',
      ),
    ),
  ),
)
		];
	}	
}

