<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class EmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'empresa',

			'id_name'			=> 'idEmpresa',

			'fields'			=> ['idEmpresa', 'nombre', 'activo', 'rut_emisor', 'rzn_soc', 'giro_emis', 'acteco', 'dir_origen', 'cmna_origen', 'telefono', 'cdg_sii_sucur', 'api_key', 'contexto', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idEmpresa' => 'INT',
				'nombre' => 'STR',
				'activo' => 'INT',
				'rut_emisor' => 'STR',
				'rzn_soc' => 'STR',
				'giro_emis' => 'STR',
				'acteco' => 'INT',
				'dir_origen' => 'STR',
				'cmna_origen' => 'STR',
				'telefono' => 'STR',
				'cdg_sii_sucur' => 'INT',
				'api_key' => 'STR',
				'contexto' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idEmpresa'],

			'autoincrement' 	=> 'idEmpresa',

			'nullable'			=> ['idEmpresa', 'api_key', 'created_at', 'updated_at'],

			'required'			=> ['nombre', 'activo', 'rut_emisor', 'rzn_soc', 'giro_emis', 'acteco', 'dir_origen', 'cmna_origen', 'telefono', 'cdg_sii_sucur', 'contexto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idEmpresa' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 255, 'required' => true],
				'activo' => ['type' => 'int', 'required' => true],
				'rut_emisor' => ['type' => 'str', 'max' => 15, 'required' => true],
				'rzn_soc' => ['type' => 'str', 'max' => 100, 'required' => true],
				'giro_emis' => ['type' => 'str', 'max' => 100, 'required' => true],
				'acteco' => ['type' => 'int', 'required' => true],
				'dir_origen' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cmna_origen' => ['type' => 'str', 'max' => 50, 'required' => true],
				'telefono' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cdg_sii_sucur' => ['type' => 'int', 'required' => true],
				'api_key' => ['type' => 'str', 'max' => 200],
				'contexto' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

			'relationships' => [
				'empresa_producto' => [
					['empresa_producto.idEmpresa','empresa.idEmpresa']
				]
			],

			'expanded_relationships' => array (
  'empresa_producto' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa_producto',
        1 => 'idEmpresa',
      ),
      1 => 
      array (
        0 => 'empresa',
        1 => 'idEmpresa',
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

