<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_SUBREGIONESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_SUBREGIONES',

			'id_name'		=> 'ID_SBR',

			'fields'		=> ['ID_SBR', 'SBR_NOMBRE', 'SBR_BORRADO'],

			'attr_types'	=> [
				'ID_SBR' => 'INT',
				'SBR_NOMBRE' => 'STR',
				'SBR_BORRADO' => 'INT'
			],

			'primary'		=> ['ID_SBR'],

			'autoincrement' => 'ID_SBR',

			'nullable'		=> ['ID_SBR', 'SBR_BORRADO'],

			'required'		=> ['SBR_NOMBRE'],

			'uniques'		=> ['SBR_NOMBRE'],

			'rules' 		=> [
				'ID_SBR' => ['type' => 'int', 'min' => 0],
				'SBR_NOMBRE' => ['type' => 'str', 'max' => 255, 'required' => true],
				'SBR_BORRADO' => ['type' => 'bool']
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

