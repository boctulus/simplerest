<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ClienteEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cliente_empresa',

			'id_name'			=> 'idCliente_empresa',

			'fields'			=> ['idCliente_empresa', 'idEmpresa', 'idCliente', 'direccion', 'email', 'coordenadas', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCliente_empresa' => 'INT',
				'idEmpresa' => 'INT',
				'idCliente' => 'INT',
				'direccion' => 'STR',
				'email' => 'STR',
				'coordenadas' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCliente_empresa'],

			'autoincrement' 	=> 'idCliente_empresa',

			'nullable'			=> ['idCliente_empresa', 'coordenadas', 'created_at', 'updated_at'],

			'required'			=> ['idEmpresa', 'idCliente', 'direccion', 'email'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCliente_empresa' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'idCliente' => ['type' => 'int', 'required' => true],
				'direccion' => ['type' => 'str', 'max' => 200, 'required' => true],
				'email' => ['type' => 'str', 'max' => 200, 'required' => true],
				'coordenadas' => ['type' => 'str', 'max' => 200],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

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

