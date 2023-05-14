<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PROYECTOS_EJECUTADOS_RECUR_PUBLICOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PROYECTOS_EJECUTADOS_RECUR_PUBLICOS',

			'id_name'		=> 'ID_PRB',

			'fields'		=> ['ID_PRB', 'PRB_ANNO', 'PRB_DURACION', 'PRB_VALOR', 'PRB_ENTIDAD', 'PRB_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_PRB' => 'INT',
				'PRB_ANNO' => 'INT',
				'PRB_DURACION' => 'STR',
				'PRB_VALOR' => 'INT',
				'PRB_ENTIDAD' => 'STR',
				'PRB_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_PRB'],

			'autoincrement' => 'ID_PRB',

			'nullable'		=> ['ID_PRB', 'PRB_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['PRB_ANNO', 'PRB_DURACION', 'PRB_VALOR', 'PRB_ENTIDAD'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_PRB' => ['type' => 'int', 'min' => 0],
				'PRB_ANNO' => ['type' => 'int', 'required' => true],
				'PRB_DURACION' => ['type' => 'str', 'max' => 30, 'required' => true],
				'PRB_VALOR' => ['type' => 'int', 'required' => true],
				'PRB_ENTIDAD' => ['type' => 'str', 'max' => 40, 'required' => true],
				'PRB_BORRADO' => ['type' => 'bool'],
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

