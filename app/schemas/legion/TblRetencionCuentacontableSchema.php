<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRetencionCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_retencion_cuentacontable',

			'id_name'		=> 'rec_intId',

			'attr_types'	=> [
				'rec_intId' => 'INT',
				'rec_intIdRetencion' => 'INT',
				'rec_intIdCuentaContable' => 'INT',
				'rec_dtimFechaCreacion' => 'STR',
				'rec_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['rec_intId', 'rec_intIdRetencion', 'rec_intIdCuentaContable'],

			'autoincrement' => 'rec_intId',

			'nullable'		=> ['rec_intId', 'rec_dtimFechaCreacion', 'rec_dtimFechaActualizacion'],

			'uniques'		=> [],

			'rules' 		=> [
				'rec_intId' => ['type' => 'int'],
				'rec_intIdRetencion' => ['type' => 'int', 'required' => true],
				'rec_intIdCuentaContable' => ['type' => 'int', 'required' => true],
				'rec_dtimFechaCreacion' => ['type' => 'datetime'],
				'rec_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['rec_intIdRetencion', 'rec_intIdCuentaContable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_retencion' => [
					['tbl_retencion.ret_intId','tbl_retencion_cuentacontable.rec_intIdRetencion']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion_cuentacontable.rec_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion_cuentacontable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion_cuentacontable.usu_intIdCreador']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.ret_intIdRetencionCuentacontable','tbl_retencion_cuentacontable.rec_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_retencion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'ret_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intIdRetencion',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intIdCuentaContable',
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
				        0 => 'tbl_retencion_cuentacontable',
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
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_llave_impuesto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_llave_impuesto',
				        1 => 'ret_intIdRetencionCuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_retencion' => [
					['tbl_retencion.ret_intId','tbl_retencion_cuentacontable.rec_intIdRetencion']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion_cuentacontable.rec_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion_cuentacontable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion_cuentacontable.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_retencion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'ret_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intIdRetencion',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intIdCuentaContable',
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
				        0 => 'tbl_retencion_cuentacontable',
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
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

