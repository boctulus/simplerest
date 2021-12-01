<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblLlaveImpuestoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_llave_impuesto',

			'id_name'		=> 'lla_intId',

			'attr_types'	=> [
				'lla_intId' => 'INT',
				'lla_varNombreLLave' => 'STR',
				'lla_dtimFechaCreacion' => 'STR',
				'lla_dtimFechaActualizacion' => 'STR',
				'ret_intIdRetencionCuentacontable' => 'INT',
				'iva_intIdIvaCuentaContable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['lla_intId'],

			'autoincrement' => 'lla_intId',

			'nullable'		=> ['lla_intId', 'lla_dtimFechaCreacion', 'lla_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> ['lla_varNombreLLave'],

			'rules' 		=> [
				'lla_intId' => ['type' => 'int'],
				'lla_varNombreLLave' => ['type' => 'str', 'max' => 50, 'required' => true],
				'lla_dtimFechaCreacion' => ['type' => 'datetime'],
				'lla_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ret_intIdRetencionCuentacontable' => ['type' => 'int', 'required' => true],
				'iva_intIdIvaCuentaContable' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'iva_intIdIvaCuentaContable', 'ret_intIdRetencionCuentacontable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_llave_impuesto.est_intIdEstado']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intId','tbl_llave_impuesto.iva_intIdIvaCuentaContable']
				],
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intId','tbl_llave_impuesto.ret_intIdRetencionCuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_llave_impuesto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_llave_impuesto.usu_intIdCreador']
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
				        0 => 'tbl_llave_impuesto',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_iva_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_llave_impuesto',
				        1 => 'iva_intIdIvaCuentaContable',
				      ),
				    ),
				  ),
				  'tbl_retencion_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_llave_impuesto',
				        1 => 'ret_intIdRetencionCuentacontable',
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
				        0 => 'tbl_llave_impuesto',
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
				        0 => 'tbl_llave_impuesto',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_llave_impuesto.est_intIdEstado']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intId','tbl_llave_impuesto.iva_intIdIvaCuentaContable']
				],
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intId','tbl_llave_impuesto.ret_intIdRetencionCuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_llave_impuesto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_llave_impuesto.usu_intIdCreador']
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
				        0 => 'tbl_llave_impuesto',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_iva_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_llave_impuesto',
				        1 => 'iva_intIdIvaCuentaContable',
				      ),
				    ),
				  ),
				  'tbl_retencion_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_llave_impuesto',
				        1 => 'ret_intIdRetencionCuentacontable',
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
				        0 => 'tbl_llave_impuesto',
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
				        0 => 'tbl_llave_impuesto',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

