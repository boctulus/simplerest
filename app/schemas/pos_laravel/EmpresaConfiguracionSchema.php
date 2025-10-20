<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class EmpresaConfiguracionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'empresa_configuracion',

			'id_name'			=> 'idEmpConf',

			'fields'			=> ['idEmpConf', 'idEmpresa', 'idConfiguracion', 'valor', 'idUnidad', 'activo', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idEmpConf' => 'INT',
				'idEmpresa' => 'INT',
				'idConfiguracion' => 'INT',
				'valor' => 'STR',
				'idUnidad' => 'INT',
				'activo' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idEmpConf', 'idEmpresa', 'idConfiguracion'],

			'autoincrement' 	=> 'idEmpConf',

			'nullable'			=> ['idEmpConf', 'valor', 'idUnidad', 'activo', 'created_at', 'updated_at'],

			'required'			=> ['idEmpresa', 'idConfiguracion'],

			'uniques'			=> [],

			'rules' 			=> [
				'idEmpConf' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'idConfiguracion' => ['type' => 'int', 'required' => true],
				'valor' => ['type' => 'str', 'max' => 10],
				'idUnidad' => ['type' => 'int'],
				'activo' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idConfiguracion'],

			'relationships' => [
				'configuracion' => [
					['configuracion.idConfiguracion','empresa_configuracion.idConfiguracion']
				]
			],

			'expanded_relationships' => array (
  'configuracion' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'configuracion',
        1 => 'idConfiguracion',
      ),
      1 => 
      array (
        0 => 'empresa_configuracion',
        1 => 'idConfiguracion',
      ),
    ),
  ),
),

			'relationships_from' => [
				'configuracion' => [
					['configuracion.idConfiguracion','empresa_configuracion.idConfiguracion']
				]
			],

			'expanded_relationships_from' => array (
  'configuracion' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'configuracion',
        1 => 'idConfiguracion',
      ),
      1 => 
      array (
        0 => 'empresa_configuracion',
        1 => 'idConfiguracion',
      ),
    ),
  ),
)
		];
	}	
}

