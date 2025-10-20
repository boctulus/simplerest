<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ConfiguracionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'configuracion',

			'id_name'			=> 'idConfiguracion',

			'fields'			=> ['idConfiguracion', 'nombre', 'activo', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idConfiguracion' => 'INT',
				'nombre' => 'STR',
				'activo' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idConfiguracion'],

			'autoincrement' 	=> 'idConfiguracion',

			'nullable'			=> ['idConfiguracion', 'nombre', 'activo', 'created_at', 'updated_at'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idConfiguracion' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> [],

			'relationships' => [
				'empresa_configuracion' => [
					['empresa_configuracion.idConfiguracion','configuracion.idConfiguracion']
				]
			],

			'expanded_relationships' => array (
  'empresa_configuracion' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa_configuracion',
        1 => 'idConfiguracion',
      ),
      1 => 
      array (
        0 => 'configuracion',
        1 => 'idConfiguracion',
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

