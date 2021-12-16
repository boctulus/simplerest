<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuarioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuario',

			'id_name'		=> 'usu_intId',

			'attr_types'	=> [
				'usu_intId' => 'INT',
				'usu_varNroIdentificacion' => 'STR',
				'usu_varNombre' => 'STR',
				'usu_varNombre2' => 'STR',
				'usu_varApellido' => 'STR',
				'usu_varApellido2' => 'STR',
				'usu_varNombreCompleto' => 'STR',
				'usu_varEmail' => 'STR',
				'usu_varNumeroCelular' => 'STR',
				'usu_varExtension' => 'STR',
				'usu_varPassword' => 'STR',
				'usu_varToken' => 'STR',
				'usu_varTokenContrasena' => 'STR',
				'usu_bolGetContrasena' => 'INT',
				'usu_bolEstadoUsuario' => 'INT',
				'usu_varImagen' => 'STR',
				'usu_intNumeroIntentos' => 'INT',
				'usu_dtimFechaCreacion' => 'STR',
				'usu_dtimFechaActualizacion' => 'STR',
				'usu_dtimFechaRecuperacion' => 'STR',
				'rol_intIdRol' => 'INT',
				'car_intIdCargo' => 'INT',
				'cdo_intIdTipoDocumento' => 'INT',
				'est_intIdEstado' => 'INT'
			],

			'primary'		=> ['usu_intId'],

			'autoincrement' => 'usu_intId',

			'nullable'		=> ['usu_intId', 'usu_varNroIdentificacion', 'usu_varNombre', 'usu_varNombre2', 'usu_varApellido', 'usu_varApellido2', 'usu_varNombreCompleto', 'usu_varEmail', 'usu_varNumeroCelular', 'usu_varExtension', 'usu_varPassword', 'usu_varToken', 'usu_varTokenContrasena', 'usu_bolGetContrasena', 'usu_varImagen', 'usu_intNumeroIntentos', 'usu_dtimFechaCreacion', 'usu_dtimFechaActualizacion', 'usu_dtimFechaRecuperacion', 'rol_intIdRol', 'car_intIdCargo', 'cdo_intIdTipoDocumento', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'usu_intId' => ['type' => 'int'],
				'usu_varNroIdentificacion' => ['type' => 'str', 'max' => 50],
				'usu_varNombre' => ['type' => 'str', 'max' => 50],
				'usu_varNombre2' => ['type' => 'str', 'max' => 50],
				'usu_varApellido' => ['type' => 'str', 'max' => 50],
				'usu_varApellido2' => ['type' => 'str', 'max' => 50],
				'usu_varNombreCompleto' => ['type' => 'str', 'max' => 100],
				'usu_varEmail' => ['type' => 'str', 'max' => 50],
				'usu_varNumeroCelular' => ['type' => 'str', 'max' => 20],
				'usu_varExtension' => ['type' => 'str', 'max' => 20],
				'usu_varPassword' => ['type' => 'str', 'max' => 20],
				'usu_varToken' => ['type' => 'str', 'max' => 50],
				'usu_varTokenContrasena' => ['type' => 'str', 'max' => 100],
				'usu_bolGetContrasena' => ['type' => 'bool'],
				'usu_bolEstadoUsuario' => ['type' => 'bool', 'required' => true],
				'usu_varImagen' => ['type' => 'str', 'max' => 250],
				'usu_intNumeroIntentos' => ['type' => 'int'],
				'usu_dtimFechaCreacion' => ['type' => 'datetime'],
				'usu_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_dtimFechaRecuperacion' => ['type' => 'datetime'],
				'rol_intIdRol' => ['type' => 'int'],
				'car_intIdCargo' => ['type' => 'int'],
				'cdo_intIdTipoDocumento' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int']
			],

			'fks' 			=> ['car_intIdCargo', 'est_intIdEstado', 'rol_intIdRol', 'cdo_intIdTipoDocumento'],

			'relationships' => [
				'tbl_cargo' => [
					['tbl_cargo.car_intId','tbl_usuario.car_intIdCargo'],
					['tbl_cargo.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_cargo.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_usuario.est_intIdEstado']
				],
				'tbl_rol' => [
					['tbl_rol.rol_intId','tbl_usuario.rol_intIdRol']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.tid_intId','tbl_usuario.cdo_intIdTipoDocumento'],
					['tbl_tipo_documento.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_tipo_documento.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_bodega' => [
					['tbl_bodega.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_bodega.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_preferencias.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_concepto_nomina' => [
					['tbl_concepto_nomina.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_concepto_nomina.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_categoria_producto' => [
					['tbl_categoria_producto.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_categoria_producto.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_pais' => [
					['tbl_pais.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_pais.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_empresa' => [
					['tbl_empresa.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_empresa.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_comprobante_contable_detalle' => [
					['tbl_comprobante_contable_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_comprobante_contable_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_cliente_retencion_cuentacontable' => [
					['tbl_cliente_retencion_cuentacontable.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_cliente_retencion_cuentacontable.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_informacion_tributaria' => [
					['tbl_informacion_tributaria.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_informacion_tributaria.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_arl' => [
					['tbl_arl.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_arl.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_ciudad.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_grupo_cuenta_contable' => [
					['tbl_grupo_cuenta_contable.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_grupo_cuenta_contable.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_empleado_informacion_pago' => [
					['tbl_empleado_informacion_pago.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_empleado_informacion_pago.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_documento' => [
					['tbl_documento.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_documento.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_persona' => [
					['tbl_persona.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_persona.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_cotizacion_detalle' => [
					['tbl_cotizacion_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_cotizacion_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_rh' => [
					['tbl_rh.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_rh.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_descuento' => [
					['tbl_descuento.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_descuento.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_cuenta_contable.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_mvto_inventario_detalle' => [
					['tbl_mvto_inventario_detalle.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_mvto_inventario_detalle.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_ubicacion' => [
					['tbl_ubicacion.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_ubicacion.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_periodo_pago' => [
					['tbl_periodo_pago.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_periodo_pago.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_contacto.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_mvto_inventario.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_factura' => [
					['tbl_factura.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_factura.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_comprobante_contable' => [
					['tbl_comprobante_contable.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_comprobante_contable.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_categoria_cuenta_contable' => [
					['tbl_categoria_cuenta_contable.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_categoria_cuenta_contable.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_iva' => [
					['tbl_iva.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_iva.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_orden_compra.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_genero' => [
					['tbl_genero.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_genero.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_empleado_datos_personales' => [
					['tbl_empleado_datos_personales.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_empleado_datos_personales.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_barrio' => [
					['tbl_barrio.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_barrio.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_forma_de_pago' => [
					['tbl_forma_de_pago.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_forma_de_pago.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_orden_compra_detalle' => [
					['tbl_orden_compra_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_orden_compra_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_nota_debito.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_cotizacion' => [
					['tbl_cotizacion.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_cotizacion.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_tipo_persona.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_tipo_contrato' => [
					['tbl_tipo_contrato.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_tipo_contrato.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_operador_pila.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_moneda' => [
					['tbl_moneda.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_moneda.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_cuenta_bancaria.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_nota_credito.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_eps' => [
					['tbl_eps.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_eps.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_estudios' => [
					['tbl_estudios.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_estudios.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_estado_civil' => [
					['tbl_estado_civil.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_estado_civil.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_retencion' => [
					['tbl_retencion.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_retencion.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_grupo_empleado' => [
					['tbl_grupo_empleado.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_grupo_empleado.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_cliente.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_compras_detalle' => [
					['tbl_compras_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_compras_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_factura_detalle.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_area' => [
					['tbl_area.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_area.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_clase_libreta_militar' => [
					['tbl_clase_libreta_militar.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_clase_libreta_militar.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_categoria_persona' => [
					['tbl_categoria_persona.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_categoria_persona.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_novedades_nomina' => [
					['tbl_novedades_nomina.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_novedades_nomina.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_contrato_empleado' => [
					['tbl_contrato_empleado.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_contrato_empleado.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_pension' => [
					['tbl_pension.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_pension.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_contrato_detalle' => [
					['tbl_contrato_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_contrato_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_dias_pago.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_pedido_detalle' => [
					['tbl_pedido_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_pedido_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_permiso' => [
					['tbl_permiso.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_permiso.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_resolucion' => [
					['tbl_resolucion.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_resolucion.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_frecuencia' => [
					['tbl_frecuencia.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_frecuencia.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_banco' => [
					['tbl_banco.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_banco.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_grupo_producto.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_consecutivo.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_pedido' => [
					['tbl_pedido.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_pedido.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_concepto' => [
					['tbl_concepto.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_concepto.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_compras' => [
					['tbl_compras.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_compras.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_contrato' => [
					['tbl_contrato.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_contrato.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_empleado_datos_generales' => [
					['tbl_empleado_datos_generales.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_empleado_datos_generales.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_especialidad' => [
					['tbl_especialidad.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_especialidad.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_motivo_retiro' => [
					['tbl_motivo_retiro.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_motivo_retiro.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_nota_debito_detalle' => [
					['tbl_nota_debito_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_nota_debito_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_departamento.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_producto' => [
					['tbl_producto.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_producto.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_medio_pago' => [
					['tbl_medio_pago.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_medio_pago.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_centro_costos' => [
					['tbl_centro_costos.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_centro_costos.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_empresa_nomina.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_transaccion' => [
					['tbl_transaccion.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_transaccion.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_reteiva' => [
					['tbl_reteiva.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_reteiva.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_nota_credito_detalle' => [
					['tbl_nota_credito_detalle.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_nota_credito_detalle.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_unidad_medida' => [
					['tbl_unidad_medida.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_unidad_medida.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_categoria_cuenta_bancaria.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_sub_cuenta_contable.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_clase_cuenta_contable' => [
					['tbl_clase_cuenta_contable.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_clase_cuenta_contable.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_categoria_licencia_conduccion' => [
					['tbl_categoria_licencia_conduccion.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_categoria_licencia_conduccion.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_sede' => [
					['tbl_sede.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_sede.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.usu_intIdCreador','tbl_usuario.usu_intId'],
					['tbl_tipo_cuenta_bancaria.usu_intIdActualizador','tbl_usuario.usu_intId']
				],
				'tbl_rete_ica' => [
					['tbl_rete_ica.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_rete_ica.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_estrato_economico' => [
					['tbl_estrato_economico.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_estrato_economico.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_proveedor.usu_intIdCreador','tbl_usuario.usu_intId']
				],
				'tbl_naturaleza_cuenta_contable' => [
					['tbl_naturaleza_cuenta_contable.usu_intIdActualizador','tbl_usuario.usu_intId'],
					['tbl_naturaleza_cuenta_contable.usu_intIdCreador','tbl_usuario.usu_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_cargo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'car_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'car_intIdCargo',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    2 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        0 => 'tbl_usuario',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_rol' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'rol_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'rol_intIdRol',
				      ),
				    ),
				  ),
				  'tbl_tipo_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_documento',
				        1 => 'tid_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'cdo_intIdTipoDocumento',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_documento',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    2 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_documento',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_bodega' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_preferencias' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_preferencias',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_preferencias',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_compras_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_persona',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_persona',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_pedido_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_estrato_economico' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estrato_economico',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estrato_economico',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_permiso' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_permiso',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_permiso',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_informacion_tributaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_concepto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_concepto',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_concepto',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_pension' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pension',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pension',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_arl' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_arl',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_arl',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_pedido' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_empleado_informacion_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_descuento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_descuento',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_descuento',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_genero' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_genero',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_genero',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_rh' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rh',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rh',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_cotizacion_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_mvto_inventario_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_ubicacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ubicacion',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ubicacion',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_forma_de_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_forma_de_pago',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_forma_de_pago',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_mvto_inventario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_factura' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_iva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contacto',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_barrio' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_barrio',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_barrio',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_compras' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_clase_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_orden_compra_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_debito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_empleado_datos_personales' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_tipo_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_cotizacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_tipo_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_contrato',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_contrato',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_concepto_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_concepto_nomina',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_concepto_nomina',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_moneda' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_moneda',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_moneda',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_grupo_empleado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_empleado',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_empleado',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_credito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_factura_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_retencion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_estado_civil' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado_civil',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado_civil',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_periodo_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_periodo_pago',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_periodo_pago',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_cuenta_bancaria',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_comprobante_contable_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_eps' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_eps',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_eps',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_area' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_area',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_area',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_estudios' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estudios',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estudios',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_licencia_conduccion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_licencia_conduccion',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_licencia_conduccion',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_frecuencia' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_frecuencia',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_frecuencia',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_novedades_nomina',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_dias_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_dias_pago',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_dias_pago',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_pais' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_resolucion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_resolucion',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_resolucion',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_banco' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_grupo_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_producto',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_producto',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato_empleado',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_comprobante_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_empleado_datos_generales' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_motivo_retiro' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_motivo_retiro',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_motivo_retiro',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_debito_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_departamento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_cliente_retencion_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_producto',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_producto',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_medio_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_especialidad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_especialidad',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_especialidad',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_transaccion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_transaccion',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_transaccion',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_reteiva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_reteiva',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_reteiva',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_credito_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_grupo_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_unidad_medida' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_unidad_medida',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_unidad_medida',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_orden_compra' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_clase_libreta_militar' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_libreta_militar',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_libreta_militar',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
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
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_sede' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sede',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sede',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_tipo_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_rete_ica' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rete_ica',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rete_ica',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_operador_pila' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_proveedor' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				  'tbl_naturaleza_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_naturaleza_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_naturaleza_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_cargo' => [
					['tbl_cargo.car_intId','tbl_usuario.car_intIdCargo']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_usuario.est_intIdEstado']
				],
				'tbl_rol' => [
					['tbl_rol.rol_intId','tbl_usuario.rol_intIdRol']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.tid_intId','tbl_usuario.cdo_intIdTipoDocumento']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_cargo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'car_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'car_intIdCargo',
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
				        0 => 'tbl_usuario',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_rol' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'rol_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'rol_intIdRol',
				      ),
				    ),
				  ),
				  'tbl_tipo_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_documento',
				        1 => 'tid_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'cdo_intIdTipoDocumento',
				      ),
				    ),
				  ),
				)
		];
	}	
}

