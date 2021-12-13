<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCargoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cargo',

			'id_name'		=> 'car_intId',

			'attr_types'	=> [
				'car_intId' => 'INT',
				'car_varCodigo' => 'STR',
				'car_varNombre' => 'STR',
				'car_lonDescripcion' => 'STR',
				'car_dtimFechaCreacion' => 'STR',
				'car_dtimFechaActualizacion' => 'STR',
				'emn_intIdEmpresa' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['car_intId'],

			'autoincrement' => 'car_intId',

			'nullable'		=> ['car_intId', 'car_dtimFechaCreacion', 'car_dtimFechaActualizacion', 'emn_intIdEmpresa', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'car_intId' => ['type' => 'int'],
				'car_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'car_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'car_lonDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'car_dtimFechaCreacion' => ['type' => 'datetime'],
				'car_dtimFechaActualizacion' => ['type' => 'datetime'],
				'emn_intIdEmpresa' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['emn_intIdEmpresa', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.emn_intId','tbl_cargo.emn_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cargo.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cargo.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cargo.usu_intIdCreador'],
					['tbl_usuario.car_intIdCargo','tbl_cargo.car_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.car_intIdcargo','tbl_cargo.car_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'emn_intIdEmpresa',
				      ),
				    ),
				  ),
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    2 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'car_intIdCargo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'car_intId',
				      ),
				    ),
				  ),
				  'tbl_contacto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contacto',
				        1 => 'car_intIdcargo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'car_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.emn_intId','tbl_cargo.emn_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cargo.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cargo.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cargo.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'emn_intIdEmpresa',
				      ),
				    ),
				  ),
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

