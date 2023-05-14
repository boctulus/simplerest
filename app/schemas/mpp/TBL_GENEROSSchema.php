<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_GENEROSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_GENEROS',

			'id_name'		=> 'ID_GEN',

			'fields'		=> ['ID_GEN', 'GEN_NOMBRE', 'GEN_BORRADO'],

			'attr_types'	=> [
				'ID_GEN' => 'INT',
				'GEN_NOMBRE' => 'STR',
				'GEN_BORRADO' => 'INT'
			],

			'primary'		=> ['ID_GEN'],

			'autoincrement' => 'ID_GEN',

			'nullable'		=> ['ID_GEN', 'GEN_BORRADO'],

			'required'		=> ['GEN_NOMBRE'],

			'uniques'		=> ['GEN_NOMBRE'],

			'rules' 		=> [
				'ID_GEN' => ['type' => 'int', 'min' => 0],
				'GEN_NOMBRE' => ['type' => 'str', 'max' => 30, 'required' => true],
				'GEN_BORRADO' => ['type' => 'bool']
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

