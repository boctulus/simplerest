<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class RolSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'rol',

			'id_name'			=> 'idRol',

			'fields'			=> ['idRol', 'nombre', 'descripcion'],

			'attr_types'		=> [
				'idRol' => 'INT',
				'nombre' => 'STR',
				'descripcion' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idRol'],

			'autoincrement' 	=> 'idRol',

			'nullable'			=> ['idRol', 'nombre', 'descripcion'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idRol' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'descripcion' => ['type' => 'str', 'max' => 50]
			],

			'fks' 				=> [],

			'relationships' => [
				'usuario_rol' => [
					['usuario_rol.rol_idRol','rol.idRol']
				]
			],

			'expanded_relationships' => array (
  'usuario_rol' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario_rol',
        1 => 'rol_idRol',
      ),
      1 => 
      array (
        0 => 'rol',
        1 => 'idRol',
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

