<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class DepartamentosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'departamentos',

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
				'nombre' => ['type' => 'str', 'max' => 60, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'org_comunales' => [
					['org_comunales.departamento_id','departamentos.id']
				],
				'representantes_legales' => [
					['representantes_legales.departamento_exp_id','departamentos.id']
				]
			],

			'expanded_relationships' => array (
  'org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunales',
        1 => 'departamento_id',
      ),
      1 => 
      array (
        0 => 'departamentos',
        1 => 'id',
      ),
    ),
  ),
  'representantes_legales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'representantes_legales',
        1 => 'departamento_exp_id',
      ),
      1 => 
      array (
        0 => 'departamentos',
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

