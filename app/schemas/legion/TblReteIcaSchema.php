<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblReteIcaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rete_ica',

			'id_name'		=> 'ric_intId',

			'attr_types'	=> [
				'ric_intId' => 'INT',
				'ric_varReteica' => 'STR',
				'ric_intTope' => 'INT',
				'ric_decPorcentaje' => 'STR',
				'ric_dtimFechaCreacion' => 'STR',
				'ric_dtimFechaActualizacion' => 'STR',
				'sub_intIdSubCuentaContable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ric_intId'],

			'autoincrement' => 'ric_intId',

			'nullable'		=> ['ric_intId', 'ric_dtimFechaCreacion', 'ric_dtimFechaActualizacion', 'sub_intIdSubCuentaContable', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'ric_intId' => ['type' => 'int'],
				'ric_varReteica' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ric_intTope' => ['type' => 'int', 'required' => true],
				'ric_decPorcentaje' => ['type' => 'decimal(18,2)', 'required' => true],
				'ric_dtimFechaCreacion' => ['type' => 'datetime'],
				'ric_dtimFechaActualizacion' => ['type' => 'datetime'],
				'sub_intIdSubCuentaContable' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'sub_intIdSubCuentaContable', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rete_ica.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_rete_ica.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rete_ica.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rete_ica.usu_intIdCreador']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.ica_intIdICA','tbl_rete_ica.ric_intId']
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
				        0 => 'tbl_rete_ica',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_rete_ica',
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
				        0 => 'tbl_rete_ica',
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
				        0 => 'tbl_rete_ica',
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
				        0 => 'tbl_rete_ica',
				        1 => 'ric_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rete_ica.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_rete_ica.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rete_ica.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rete_ica.usu_intIdCreador']
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
				        0 => 'tbl_rete_ica',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_rete_ica',
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
				        0 => 'tbl_rete_ica',
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
				        0 => 'tbl_rete_ica',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

