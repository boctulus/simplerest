<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PROYECTOS_EJECUTADOS_RECUR_PROPIOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PROYECTOS_EJECUTADOS_RECUR_PROPIOS',

			'id_name'		=> 'ID_PRV',

			'fields'		=> ['ID_PRV', 'PRV_ANNO', 'PRV_DURACION', 'PRV_VALOR', 'PRV_ENTIDAD', 'PRV_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_PRV' => 'INT',
				'PRV_ANNO' => 'INT',
				'PRV_DURACION' => 'STR',
				'PRV_VALOR' => 'INT',
				'PRV_ENTIDAD' => 'STR',
				'PRV_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_PRV'],

			'autoincrement' => 'ID_PRV',

			'nullable'		=> ['ID_PRV', 'PRV_ENTIDAD', 'PRV_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['PRV_ANNO', 'PRV_DURACION', 'PRV_VALOR'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_PRV' => ['type' => 'int', 'min' => 0],
				'PRV_ANNO' => ['type' => 'int', 'required' => true],
				'PRV_DURACION' => ['type' => 'str', 'max' => 30, 'required' => true],
				'PRV_VALOR' => ['type' => 'int', 'required' => true],
				'PRV_ENTIDAD' => ['type' => 'str', 'max' => 40],
				'PRV_BORRADO' => ['type' => 'bool'],
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

