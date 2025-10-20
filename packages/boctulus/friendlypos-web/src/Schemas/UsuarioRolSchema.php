<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UsuarioRolSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'usuario_rol',

			'id_name'			=> 'idUsuario_rol',

			'fields'			=> ['idUsuario_rol', 'Usuarios_idUsuarios', 'rol_idRol'],

			'attr_types'		=> [
				'idUsuario_rol' => 'INT',
				'Usuarios_idUsuarios' => 'INT',
				'rol_idRol' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idUsuario_rol', 'Usuarios_idUsuarios', 'rol_idRol'],

			'autoincrement' 	=> 'idUsuario_rol',

			'nullable'			=> ['idUsuario_rol'],

			'required'			=> ['Usuarios_idUsuarios', 'rol_idRol'],

			'uniques'			=> [],

			'rules' 			=> [
				'idUsuario_rol' => ['type' => 'int'],
				'Usuarios_idUsuarios' => ['type' => 'int', 'required' => true],
				'rol_idRol' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['rol_idRol', 'Usuarios_idUsuarios'],

			'relationships' => [
				'rol' => [
					['rol.idRol','usuario_rol.rol_idRol']
				],
				'usuario' => [
					['usuario.idUsuario','usuario_rol.Usuarios_idUsuarios']
				]
			],

			'expanded_relationships' => array (
  'rol' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'rol',
        1 => 'idRol',
      ),
      1 => 
      array (
        0 => 'usuario_rol',
        1 => 'rol_idRol',
      ),
    ),
  ),
  'usuario' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario',
        1 => 'idUsuario',
      ),
      1 => 
      array (
        0 => 'usuario_rol',
        1 => 'Usuarios_idUsuarios',
      ),
    ),
  ),
),

			'relationships_from' => [
				'rol' => [
					['rol.idRol','usuario_rol.rol_idRol']
				],
				'usuario' => [
					['usuario.idUsuario','usuario_rol.Usuarios_idUsuarios']
				]
			],

			'expanded_relationships_from' => array (
  'rol' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'rol',
        1 => 'idRol',
      ),
      1 => 
      array (
        0 => 'usuario_rol',
        1 => 'rol_idRol',
      ),
    ),
  ),
  'usuario' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario',
        1 => 'idUsuario',
      ),
      1 => 
      array (
        0 => 'usuario_rol',
        1 => 'Usuarios_idUsuarios',
      ),
    ),
  ),
)
		];
	}	
}

