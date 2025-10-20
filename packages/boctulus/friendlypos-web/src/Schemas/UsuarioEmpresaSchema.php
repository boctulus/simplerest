<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UsuarioEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'usuario_empresa',

			'id_name'			=> 'idUsuario_empresa',

			'fields'			=> ['idUsuario_empresa', 'idUsuarios', 'idEmpresa'],

			'attr_types'		=> [
				'idUsuario_empresa' => 'INT',
				'idUsuarios' => 'INT',
				'idEmpresa' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idUsuario_empresa', 'idUsuarios', 'idEmpresa'],

			'autoincrement' 	=> 'idUsuario_empresa',

			'nullable'			=> ['idUsuario_empresa'],

			'required'			=> ['idUsuarios', 'idEmpresa'],

			'uniques'			=> [],

			'rules' 			=> [
				'idUsuario_empresa' => ['type' => 'int'],
				'idUsuarios' => ['type' => 'int', 'required' => true],
				'idEmpresa' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['idUsuarios'],

			'relationships' => [
				'usuario' => [
					['usuario.idUsuario','usuario_empresa.idUsuarios']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'usuario_empresa',
        1 => 'idUsuarios',
      ),
    ),
  ),
),

			'relationships_from' => [
				'usuario' => [
					['usuario.idUsuario','usuario_empresa.idUsuarios']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'usuario_empresa',
        1 => 'idUsuarios',
      ),
    ),
  ),
)
		];
	}	
}

