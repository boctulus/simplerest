<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UsuarioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'usuario',

			'id_name'			=> 'idUsuario',

			'fields'			=> ['idUsuario', 'IdRol', 'Nombre', 'activo'],

			'attr_types'		=> [
				'idUsuario' => 'INT',
				'IdRol' => 'STR',
				'Nombre' => 'STR',
				'activo' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idUsuario'],

			'autoincrement' 	=> 'idUsuario',

			'nullable'			=> ['idUsuario', 'IdRol', 'Nombre', 'activo'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idUsuario' => ['type' => 'int'],
				'IdRol' => ['type' => 'str', 'max' => 45],
				'Nombre' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int']
			],

			'fks' 				=> [],

			'relationships' => [
				'usuario_empresa' => [
					['usuario_empresa.idUsuarios','usuario.idUsuario']
				],
				'usuario_rol' => [
					['usuario_rol.Usuarios_idUsuarios','usuario.idUsuario']
				]
			],

			'expanded_relationships' => array (
  'usuario_empresa' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario_empresa',
        1 => 'idUsuarios',
      ),
      1 => 
      array (
        0 => 'usuario',
        1 => 'idUsuario',
      ),
    ),
  ),
  'usuario_rol' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario_rol',
        1 => 'Usuarios_idUsuarios',
      ),
      1 => 
      array (
        0 => 'usuario',
        1 => 'idUsuario',
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

