<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRetencionIcaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_retencion_ica',

			'id_name'		=> 'ric_intId',

			'attr_types'	=> [
				'ric_intId' => 'INT',
				'ric_varReteIca' => 'STR',
				'ric_intTope' => 'INT',
				'ric_intPorcentaje' => 'STR',
				'ric_dtimFechaCreacion' => 'STR',
				'ric_dtimFechaActualizacion' => 'STR',
				'est_intIdCidEstado' => 'INT',
				'sub_intIdSubCuentaContable' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ric_intId'],

			'autoincrement' => 'ric_intId',

			'nullable'		=> ['ric_intId', 'ric_dtimFechaCreacion', 'ric_dtimFechaActualizacion', 'est_intIdCidEstado', 'sub_intIdSubCuentaContable'],

			'uniques'		=> ['ric_varReteIca'],

			'rules' 		=> [
				'ric_intId' => ['type' => 'int'],
				'ric_varReteIca' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ric_intTope' => ['type' => 'int', 'required' => true],
				'ric_intPorcentaje' => ['type' => 'decimal(10,2)', 'required' => true],
				'ric_dtimFechaCreacion' => ['type' => 'datetime'],
				'ric_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdCidEstado' => ['type' => 'int'],
				'sub_intIdSubCuentaContable' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdCidEstado', 'sub_intIdSubCuentaContable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_retencion_ica.est_intIdCidEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion_ica.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion_ica.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion_ica.usu_intIdActualizador']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.ica_intIdICA','tbl_retencion_ica.ric_intId']
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
				        0 => 'tbl_retencion_ica',
				        1 => 'est_intIdCidEstado',
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
				        0 => 'tbl_retencion_ica',
				        1 => 'sub_intIdSubCuentaContable',
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
				        0 => 'tbl_retencion_ica',
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
				        0 => 'tbl_retencion_ica',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ica_intIdICA',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion_ica',
				        1 => 'ric_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_retencion_ica.est_intIdCidEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion_ica.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion_ica.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion_ica.usu_intIdCreador']
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
				        0 => 'tbl_retencion_ica',
				        1 => 'est_intIdCidEstado',
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
				        0 => 'tbl_retencion_ica',
				        1 => 'sub_intIdSubCuentaContable',
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
				        0 => 'tbl_retencion_ica',
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
				        0 => 'tbl_retencion_ica',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

