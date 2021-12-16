<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblContratoEmpleadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_contrato_empleado',

			'id_name'		=> 'tce_intId',

			'attr_types'	=> [
				'tce_intId' => 'INT',
				'tce_varCodigo' => 'STR',
				'tce_varNombre' => 'STR',
				'tce_lonDescripcion' => 'STR',
				'tce_datFechaInicio' => 'STR',
				'tce_datFechaFin' => 'STR',
				'tce_dtimFechaCreacion' => 'STR',
				'tce_dtimFechaActualizacion' => 'STR',
				'per_intIdPersona' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tce_intId'],

			'autoincrement' => 'tce_intId',

			'nullable'		=> ['tce_intId', 'tce_dtimFechaCreacion', 'tce_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'tce_intId' => ['type' => 'int'],
				'tce_varCodigo' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tce_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tce_lonDescripcion' => ['type' => 'str', 'required' => true],
				'tce_datFechaInicio' => ['type' => 'date', 'required' => true],
				'tce_datFechaFin' => ['type' => 'date', 'required' => true],
				'tce_dtimFechaCreacion' => ['type' => 'datetime'],
				'tce_dtimFechaActualizacion' => ['type' => 'datetime'],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contrato_empleado.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_contrato_empleado.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contrato_empleado.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contrato_empleado.usu_intIdActualizador']
				],
				'tbl_novedades_nomina' => [
					['tbl_novedades_nomina.tce_intIdContrato','tbl_contrato_empleado.tce_intId']
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
				        0 => 'tbl_contrato_empleado',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato_empleado',
				        1 => 'per_intIdPersona',
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
				        0 => 'tbl_contrato_empleado',
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
				        0 => 'tbl_contrato_empleado',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_novedades_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'tce_intIdContrato',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato_empleado',
				        1 => 'tce_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contrato_empleado.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_contrato_empleado.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contrato_empleado.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contrato_empleado.usu_intIdActualizador']
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
				        0 => 'tbl_contrato_empleado',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato_empleado',
				        1 => 'per_intIdPersona',
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
				        0 => 'tbl_contrato_empleado',
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
				        0 => 'tbl_contrato_empleado',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

