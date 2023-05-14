<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PERMISOS_ROLESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PERMISOS_ROLES',

			'id_name'		=> 'PER_NUM_OPCION',

			'fields'		=> ['PER_NUM_OPCION', 'ID_PERMISO', 'ID_ROL', 'PER_COMENTARIO'],

			'attr_types'	=> [
				'PER_NUM_OPCION' => 'INT',
				'ID_PERMISO' => 'INT',
				'ID_ROL' => 'INT',
				'PER_COMENTARIO' => 'STR'
			],

			'primary'		=> ['PER_NUM_OPCION', 'ID_PERMISO', 'ID_ROL'],

			'autoincrement' => 'PER_NUM_OPCION',

			'nullable'		=> ['PER_NUM_OPCION', 'PER_COMENTARIO'],

			'required'		=> ['ID_PERMISO', 'ID_ROL'],

			'uniques'		=> [],

			'rules' 		=> [
				'PER_NUM_OPCION' => ['type' => 'int'],
				'ID_PERMISO' => ['type' => 'int', 'required' => true],
				'ID_ROL' => ['type' => 'int', 'required' => true],
				'PER_COMENTARIO' => ['type' => 'str', 'max' => 100]
			],

			'fks' 			=> ['ID_PERMISO', 'ID_ROL'],

			'relationships' => [
				'TBL_PERMISOS' => [
					['TBL_PERMISOS.PER_ID','TBL_PERMISOS_ROLES.ID_PERMISO']
				],
				'TBL_ROLES' => [
					['TBL_ROLES.ROL_ID','TBL_PERMISOS_ROLES.ID_ROL']
				]
			],

			'expanded_relationships' => array (
  'TBL_PERMISOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERMISOS',
        1 => 'PER_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERMISOS_ROLES',
        1 => 'ID_PERMISO',
      ),
    ),
  ),
  'TBL_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERMISOS_ROLES',
        1 => 'ID_ROL',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_PERMISOS' => [
					['TBL_PERMISOS.PER_ID','TBL_PERMISOS_ROLES.ID_PERMISO']
				],
				'TBL_ROLES' => [
					['TBL_ROLES.ROL_ID','TBL_PERMISOS_ROLES.ID_ROL']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_PERMISOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERMISOS',
        1 => 'PER_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERMISOS_ROLES',
        1 => 'ID_PERMISO',
      ),
    ),
  ),
  'TBL_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERMISOS_ROLES',
        1 => 'ID_ROL',
      ),
    ),
  ),
)
		];
	}	
}

