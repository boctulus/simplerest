<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PROYECTOS_EJECUTADOS_COOPERACIONSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PROYECTOS_EJECUTADOS_COOPERACION',

			'id_name'		=> 'ID_PCO',

			'fields'		=> ['ID_PCO', 'PCO_ANNO', 'PCO_DURACION', 'PCO_VALOR', 'PCO_ENTIDAD', 'PCO_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_PCO' => 'INT',
				'PCO_ANNO' => 'INT',
				'PCO_DURACION' => 'STR',
				'PCO_VALOR' => 'INT',
				'PCO_ENTIDAD' => 'STR',
				'PCO_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_PCO'],

			'autoincrement' => 'ID_PCO',

			'nullable'		=> ['ID_PCO', 'PCO_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['PCO_ANNO', 'PCO_DURACION', 'PCO_VALOR', 'PCO_ENTIDAD'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_PCO' => ['type' => 'int', 'min' => 0],
				'PCO_ANNO' => ['type' => 'int', 'required' => true],
				'PCO_DURACION' => ['type' => 'str', 'max' => 30, 'required' => true],
				'PCO_VALOR' => ['type' => 'int', 'required' => true],
				'PCO_ENTIDAD' => ['type' => 'str', 'max' => 60, 'required' => true],
				'PCO_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
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

