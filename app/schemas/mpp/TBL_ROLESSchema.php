<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ROLESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ROLES',

			'id_name'		=> 'ROL_ID',

			'fields'		=> ['ROL_ID', 'ROL_NOMBRE', 'ROL_BORRADO'],

			'attr_types'	=> [
				'ROL_ID' => 'INT',
				'ROL_NOMBRE' => 'STR',
				'ROL_BORRADO' => 'INT'
			],

			'primary'		=> ['ROL_ID'],

			'autoincrement' => 'ROL_ID',

			'nullable'		=> ['ROL_ID', 'ROL_BORRADO'],

			'required'		=> ['ROL_NOMBRE'],

			'uniques'		=> [],

			'rules' 		=> [
				'ROL_ID' => ['type' => 'int'],
				'ROL_NOMBRE' => ['type' => 'str', 'max' => 20, 'required' => true],
				'ROL_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.ROL_ID','TBL_ROLES.ROL_ID']
				],
				'TBL_PERMISOS_ROLES' => [
					['TBL_PERMISOS_ROLES.ID_ROL','TBL_ROLES.ROL_ID']
				],
				'TBL_PERFILES_ROLES' => [
					['TBL_PERFILES_ROLES.ROL_ID','TBL_ROLES.ROL_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_USUARIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
    ),
  ),
  'TBL_PERMISOS_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERMISOS_ROLES',
        1 => 'ID_ROL',
      ),
      1 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
    ),
  ),
  'TBL_PERFILES_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERFILES_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
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

