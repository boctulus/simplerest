<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ModuloSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'modulo',

			'id_name'		=> 'int_moducodigo',

			'attr_types'	=> [
				'int_moducodigo' => 'INT',
				'vch_modunombre' => 'STR',
				'vch_moduestado' => 'STR'
			],

			'primary'		=> ['int_moducodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_modunombre', 'vch_moduestado'],

			'uniques'		=> [],

			'rules' 		=> [
				'int_moducodigo' => ['type' => 'int', 'required' => true],
				'vch_modunombre' => ['type' => 'str', 'max' => 50],
				'vch_moduestado' => ['type' => 'str', 'max' => 15]
			],

			'fks' 			=> [],

			'relationships' => [
				'permiso' => [
					['permiso.int_moducodigo','modulo.int_moducodigo']
				]
			],

			'expanded_relationships' => array (
  'permiso' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'permiso',
        1 => 'int_moducodigo',
      ),
      1 => 
      array (
        0 => 'modulo',
        1 => 'int_moducodigo',
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

