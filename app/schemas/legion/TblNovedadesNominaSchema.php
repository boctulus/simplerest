<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblNovedadesNominaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_novedades_nomina',

			'id_name'		=> 'nvn_intId',

			'attr_types'	=> [
				'nvn_intId' => 'INT',
				'nvn_varCodigo' => 'STR',
				'nvn_Nombre' => 'STR',
				'nvn_lonDescripcion' => 'STR',
				'nvn_datFecha' => 'STR',
				'nvn_decCantidad' => 'STR',
				'nvn_decValor' => 'STR',
				'nvn_dtimFechaCreacion' => 'STR',
				'nvn_dtimFechaActualizacion' => 'STR',
				'tce_intIdContrato' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['nvn_intId'],

			'autoincrement' => 'nvn_intId',

			'nullable'		=> ['nvn_intId', 'nvn_dtimFechaCreacion', 'nvn_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'nvn_intId' => ['type' => 'int'],
				'nvn_varCodigo' => ['type' => 'str', 'max' => 100, 'required' => true],
				'nvn_Nombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'nvn_lonDescripcion' => ['type' => 'str', 'required' => true],
				'nvn_datFecha' => ['type' => 'date', 'required' => true],
				'nvn_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'nvn_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'nvn_dtimFechaCreacion' => ['type' => 'datetime'],
				'nvn_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tce_intIdContrato' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'tce_intIdContrato', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_novedades_nomina.est_intIdEstado']
				],
				'tbl_contrato_empleado' => [
					['tbl_contrato_empleado.tce_intId','tbl_novedades_nomina.tce_intIdContrato']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_novedades_nomina.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_novedades_nomina.usu_intIdActualizador']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'tbl_novedades_nomina',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_contrato_empleado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato_empleado',
				        1 => 'tce_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'tce_intIdContrato',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_novedades_nomina.est_intIdEstado']
				],
				'tbl_contrato_empleado' => [
					['tbl_contrato_empleado.tce_intId','tbl_novedades_nomina.tce_intIdContrato']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_novedades_nomina.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_novedades_nomina.usu_intIdActualizador']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'tbl_novedades_nomina',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_contrato_empleado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato_empleado',
				        1 => 'tce_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'tce_intIdContrato',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

