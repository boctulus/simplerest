<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PERMISOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PERMISOS',

			'id_name'		=> 'PER_ID',

			'fields'		=> ['PER_ID', 'PER_NUMERO_OPCION', 'PER_NOMBRE_OPCION', 'PER_NOMBRE', 'PER_BORRADO'],

			'attr_types'	=> [
				'PER_ID' => 'INT',
				'PER_NUMERO_OPCION' => 'INT',
				'PER_NOMBRE_OPCION' => 'STR',
				'PER_NOMBRE' => 'STR',
				'PER_BORRADO' => 'INT'
			],

			'primary'		=> ['PER_ID'],

			'autoincrement' => 'PER_ID',

			'nullable'		=> ['PER_ID', 'PER_NOMBRE_OPCION', 'PER_NOMBRE', 'PER_BORRADO'],

			'required'		=> ['PER_NUMERO_OPCION'],

			'uniques'		=> [],

			'rules' 		=> [
				'PER_ID' => ['type' => 'int'],
				'PER_NUMERO_OPCION' => ['type' => 'int', 'required' => true],
				'PER_NOMBRE_OPCION' => ['type' => 'str', 'max' => 100],
				'PER_NOMBRE' => ['type' => 'str', 'max' => 100],
				'PER_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_PERMISOS_ROLES' => [
					['TBL_PERMISOS_ROLES.ID_PERMISO','TBL_PERMISOS.PER_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_PERMISOS_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERMISOS_ROLES',
        1 => 'ID_PERMISO',
      ),
      1 => 
      array (
        0 => 'TBL_PERMISOS',
        1 => 'PER_ID',
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

