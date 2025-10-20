<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class EmpresaMarcaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'empresa_marca',

			'id_name'			=> 'idEmpresa_marca',

			'fields'			=> ['idEmpresa_marca', 'idEmpresa', 'nombre'],

			'attr_types'		=> [
				'idEmpresa_marca' => 'INT',
				'idEmpresa' => 'INT',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idEmpresa_marca', 'idEmpresa'],

			'autoincrement' 	=> 'idEmpresa_marca',

			'nullable'			=> ['idEmpresa_marca', 'nombre'],

			'required'			=> ['idEmpresa'],

			'uniques'			=> [],

			'rules' 			=> [
				'idEmpresa_marca' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'nombre' => ['type' => 'str', 'max' => 45]
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

