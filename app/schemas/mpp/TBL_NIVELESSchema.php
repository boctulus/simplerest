<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_NIVELESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_NIVELES',

			'id_name'		=> 'ID_NIV',

			'fields'		=> ['ID_NIV', 'NIV_NOMBRE', 'NIV_BORRADO'],

			'attr_types'	=> [
				'ID_NIV' => 'INT',
				'NIV_NOMBRE' => 'STR',
				'NIV_BORRADO' => 'INT'
			],

			'primary'		=> ['ID_NIV'],

			'autoincrement' => 'ID_NIV',

			'nullable'		=> ['ID_NIV', 'NIV_BORRADO'],

			'required'		=> ['NIV_NOMBRE'],

			'uniques'		=> ['NIV_NOMBRE'],

			'rules' 		=> [
				'ID_NIV' => ['type' => 'int', 'min' => 0],
				'NIV_NOMBRE' => ['type' => 'str', 'max' => 30, 'required' => true],
				'NIV_BORRADO' => ['type' => 'bool']
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

