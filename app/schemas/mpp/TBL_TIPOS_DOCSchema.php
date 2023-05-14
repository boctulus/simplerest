<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_TIPOS_DOCSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_TIPOS_DOC',

			'id_name'		=> 'ID_TDC',

			'fields'		=> ['ID_TDC', 'TDC_NOMBRE', 'TDC_BORRADO'],

			'attr_types'	=> [
				'ID_TDC' => 'INT',
				'TDC_NOMBRE' => 'STR',
				'TDC_BORRADO' => 'INT'
			],

			'primary'		=> ['ID_TDC'],

			'autoincrement' => 'ID_TDC',

			'nullable'		=> ['ID_TDC', 'TDC_BORRADO'],

			'required'		=> ['TDC_NOMBRE'],

			'uniques'		=> ['TDC_NOMBRE'],

			'rules' 		=> [
				'ID_TDC' => ['type' => 'int', 'min' => 0],
				'TDC_NOMBRE' => ['type' => 'str', 'max' => 40, 'required' => true],
				'TDC_BORRADO' => ['type' => 'bool']
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

