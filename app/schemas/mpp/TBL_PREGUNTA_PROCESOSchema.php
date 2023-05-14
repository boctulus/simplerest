<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PREGUNTA_PROCESOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PREGUNTA_PROCESO',

			'id_name'		=> 'PRE_ID',

			'fields'		=> ['PRE_ID', 'PRE_TITULO', 'PRE_ENUNCIADO', 'PRE_BORRADO', 'PRO_ID', 'PRE_MULTIPLE_RESPUESTA'],

			'attr_types'	=> [
				'PRE_ID' => 'INT',
				'PRE_TITULO' => 'STR',
				'PRE_ENUNCIADO' => 'STR',
				'PRE_BORRADO' => 'INT',
				'PRO_ID' => 'INT',
				'PRE_MULTIPLE_RESPUESTA' => 'INT'
			],

			'primary'		=> ['PRE_ID'],

			'autoincrement' => 'PRE_ID',

			'nullable'		=> ['PRE_ID', 'PRE_BORRADO', 'PRE_MULTIPLE_RESPUESTA'],

			'required'		=> ['PRE_TITULO', 'PRE_ENUNCIADO', 'PRO_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'PRE_ID' => ['type' => 'int'],
				'PRE_TITULO' => ['type' => 'str', 'max' => 200, 'required' => true],
				'PRE_ENUNCIADO' => ['type' => 'str', 'max' => 200, 'required' => true],
				'PRE_BORRADO' => ['type' => 'bool'],
				'PRO_ID' => ['type' => 'int', 'required' => true],
				'PRE_MULTIPLE_RESPUESTA' => ['type' => 'bool']
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

