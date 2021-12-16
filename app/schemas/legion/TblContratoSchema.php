<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblContratoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_contrato',

			'id_name'		=> 'ctr_intId',

			'attr_types'	=> [
				'ctr_intId' => 'INT',
				'ctr_varNumeroContrato' => 'STR',
				'ctr_datFechaInicial' => 'STR',
				'ctr_datFechaFinal' => 'STR',
				'ctr_intMesesContrato' => 'INT',
				'ctr_intDiasGracia' => 'INT',
				'ctr_intTiempoAnalisis' => 'INT',
				'ctr_intTiempoRenovacion' => 'INT',
				'ctr_intNumeroProductos' => 'INT',
				'ctr_decValorMensual' => 'STR',
				'ctr_lonNota' => 'STR',
				'ctr_dtimFechaCreacion' => 'STR',
				'ctr_dtimFechaActualizacion' => 'STR',
				'cco_intIdCentroCostos' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'cse_intidConsecutivo' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ctr_intId'],

			'autoincrement' => 'ctr_intId',

			'nullable'		=> ['ctr_intId', 'ctr_dtimFechaCreacion', 'ctr_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'ctr_intId' => ['type' => 'int'],
				'ctr_varNumeroContrato' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ctr_datFechaInicial' => ['type' => 'date', 'required' => true],
				'ctr_datFechaFinal' => ['type' => 'date', 'required' => true],
				'ctr_intMesesContrato' => ['type' => 'int', 'required' => true],
				'ctr_intDiasGracia' => ['type' => 'int', 'required' => true],
				'ctr_intTiempoAnalisis' => ['type' => 'int', 'required' => true],
				'ctr_intTiempoRenovacion' => ['type' => 'int', 'required' => true],
				'ctr_intNumeroProductos' => ['type' => 'int', 'required' => true],
				'ctr_decValorMensual' => ['type' => 'decimal(18,2)', 'required' => true],
				'ctr_lonNota' => ['type' => 'str', 'required' => true],
				'ctr_dtimFechaCreacion' => ['type' => 'datetime'],
				'ctr_dtimFechaActualizacion' => ['type' => 'datetime'],
				'cco_intIdCentroCostos' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'cse_intidConsecutivo' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cco_intIdCentroCostos', 'cse_intidConsecutivo', 'doc_intIdDocumento', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_contrato.cco_intIdCentroCostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_contrato.cse_intidConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_contrato.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contrato.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contrato.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contrato.usu_intIdActualizador']
				],
				'tbl_contrato_detalle' => [
					['tbl_contrato_detalle.ctr_intIdContrato','tbl_contrato.ctr_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'cco_intIdCentroCostos',
				      ),
				    ),
				  ),
				  'tbl_consecutivo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'cse_intidConsecutivo',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_contrato',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
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
				        0 => 'tbl_contrato',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_contrato_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato_detalle',
				        1 => 'ctr_intIdContrato',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'ctr_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_contrato.cco_intIdCentroCostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_contrato.cse_intidConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_contrato.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contrato.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contrato.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contrato.usu_intIdActualizador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'cco_intIdCentroCostos',
				      ),
				    ),
				  ),
				  'tbl_consecutivo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'cse_intidConsecutivo',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_contrato',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato',
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
				        0 => 'tbl_contrato',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

