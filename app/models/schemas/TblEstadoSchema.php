<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estado',

			'id_name'		=> 'est_intId',

			'attr_types'	=> [
				'est_intId' => 'INT',
				'est_varNombre' => 'STR',
				'est_varIcono' => 'STR',
				'est_varColor' => 'STR',
				'est_dtimFechaCreacion' => 'STR',
				'est_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['est_intId'],

			'rules' 		=> [
				'est_varNombre' => ['max' => 20],
				'est_varIcono' => ['max' => 100],
				'est_varColor' => ['max' => 100]
			],

			'relationships' => [
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_estado.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_estado.usu_intIdCreador'],
					['tbl_usuario.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_documento' => [
					['tbl_categoria_documento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_arl' => [
					['tbl_arl.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cargo' => [
					['tbl_cargo.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_moneda' => [
					['tbl_moneda.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_unidadmedida' => [
					['tbl_unidadmedida.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.est_intIdEstado_cba','tbl_estado.est_intId']
				],
				'tbl_categoria_producto' => [
					['tbl_categoria_producto.est_intIdEstado_cap','tbl_estado.est_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_genero' => [
					['tbl_genero.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estrato_economico' => [
					['tbl_estrato_economico.est_intIdestado','tbl_estado.est_intId']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_empresa' => [
					['tbl_empresa.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_producto' => [
					['tbl_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_concepto' => [
					['tbl_concepto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_rol_permiso' => [
					['tbl_rol_permiso.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_descuento' => [
					['tbl_descuento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_persona' => [
					['tbl_categoria_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pension' => [
					['tbl_pension.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_bodega' => [
					['tbl_bodega.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estado_civil' => [
					['tbl_estado_civil.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_identificacion' => [
					['tbl_categoria_identificacion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_centro_costos' => [
					['tbl_centro_costos.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_eps' => [
					['tbl_eps.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_persona' => [
					['tbl_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estudios' => [
					['tbl_estudios.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_permiso' => [
					['tbl_permiso.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_motivo_retiro' => [
					['tbl_motivo_retiro.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_contrato' => [
					['tbl_tipo_contrato.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_licencia_conduccion' => [
					['tbl_categoria_licencia_conduccion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pais' => [
					['tbl_pais.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_iva' => [
					['tbl_iva.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_clase_libreta_militar' => [
					['tbl_clase_libreta_militar.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cliente_informacion_tributaria' => [
					['tbl_cliente_informacion_tributaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_banco' => [
					['tbl_banco.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_rol' => [
					['tbl_rol.est_intIdEstado_rol','tbl_estado.est_intId']
				],
				'tbl_periodo_pago' => [
					['tbl_periodo_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_proveedor_informacion_tributaria' => [
					['tbl_proveedor_informacion_tributaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_rh' => [
					['tbl_rh.est_intIdEstado','tbl_estado.est_intId']
				]
			]
		];
	}	
}

