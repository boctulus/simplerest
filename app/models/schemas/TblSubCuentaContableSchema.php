<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblSubCuentaContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_sub_cuenta_contable',

			'id_name'		=> 'sub_varCodigoCuenta',

			'attr_types'	=> [
				'sub_intId' => 'INT',
				'sub_varCodigoCuenta' => 'STR',
				'sub_varNombreCuenta' => 'STR',
				'sub_varConceptoMedioMagnetico' => 'STR',
				'sub_varEquivalenciaFisica' => 'STR',
				'sub_tinManejaTercero' => 'INT',
				'sub_tinManejaCentroCostos' => 'INT',
				'sub_tinManejaBase' => 'INT',
				'sub_intPorcentajeBase' => 'INT',
				'sub_decMontobase' => 'STR',
				'sub_tinCuentaBalance' => 'INT',
				'sub_tinCuentaResultado' => 'INT',
				'sub_dtimFechaCreacion' => 'STR',
				'sub_dtimFechaActualizacion' => 'STR',
				'mon_intIdMoneda' => 'INT',
				'ccc_intIdCategoriaCuentaContable' => 'INT',
				'cue_intIdCuentaContable' => 'INT',
				'nat_intIdNaturalezaCuentaContable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['sub_intId', 'sub_intPorcentajeBase', 'sub_decMontobase'],

			'rules' 		=> [
				'sub_varCodigoCuenta' => ['max' => 20],
				'sub_varNombreCuenta' => ['max' => 50],
				'sub_varConceptoMedioMagnetico' => ['max' => 50],
				'sub_varEquivalenciaFisica' => ['max' => 50]
			],

			'relationships' => [
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.cue_intId','tbl_sub_cuenta_contable.cue_intIdCuentaContable']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_sub_cuenta_contable.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_sub_cuenta_contable.mon_intIdMoneda']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_sub_cuenta_contable.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_sub_cuenta_contable.usu_intIdCreador']
				],
				'tbl_producto' => [
					['tbl_producto.sub_intIdCuentaContableCompra','tbl_sub_cuenta_contable.sub_intId'],
					['tbl_producto.sub_intIdCuentaContableVenta','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_iva' => [
					['tbl_iva.sub_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_banco' => [
					['tbl_banco.sub_intIdCuentaCxC','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_cliente_informacion_tributaria' => [
					['tbl_cliente_informacion_tributaria.sub_intIdSubcuentacontable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_proveedor_informacion_tributaria' => [
					['tbl_proveedor_informacion_tributaria.sub_intIdSubCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				]
			]
		];
	}	
}

