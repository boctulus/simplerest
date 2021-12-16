<?php

namespace simplerest\schemas\legion;

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
				'est_dtimFechaActualizacion' => 'STR'
			],

			'primary'		=> ['est_intId'],

			'autoincrement' => 'est_intId',

			'nullable'		=> ['est_intId', 'est_dtimFechaCreacion', 'est_dtimFechaActualizacion'],

			'uniques'		=> [],

			'rules' 		=> [
				'est_intId' => ['type' => 'int'],
				'est_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'est_varIcono' => ['type' => 'str', 'max' => 150, 'required' => true],
				'est_varColor' => ['type' => 'str', 'max' => 150, 'required' => true],
				'est_dtimFechaCreacion' => ['type' => 'datetime'],
				'est_dtimFechaActualizacion' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				'tbl_genero' => [
					['tbl_genero.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_centro_costos' => [
					['tbl_centro_costos.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_forma_de_pago' => [
					['tbl_forma_de_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_empleado_informacion_pago' => [
					['tbl_empleado_informacion_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_naturaleza_cuenta_contable' => [
					['tbl_naturaleza_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_eps' => [
					['tbl_eps.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_rete_ica' => [
					['tbl_rete_ica.est_intIdCidEstado','tbl_estado.est_intId']
				],
				'tbl_rol' => [
					['tbl_rol.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_persona' => [
					['tbl_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_motivo_retiro' => [
					['tbl_motivo_retiro.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_licencia_conduccion' => [
					['tbl_categoria_licencia_conduccion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_unidad_medida' => [
					['tbl_unidad_medida.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estudios' => [
					['tbl_estudios.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_transaccion' => [
					['tbl_transaccion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estado_civil' => [
					['tbl_estado_civil.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_retencion' => [
					['tbl_retencion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_arl' => [
					['tbl_arl.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_grupo_empleado' => [
					['tbl_grupo_empleado.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_contrato_empleado' => [
					['tbl_contrato_empleado.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pension' => [
					['tbl_pension.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.est_intIdEstado_cba','tbl_estado.est_intId']
				],
				'tbl_empleado_datos_generales' => [
					['tbl_empleado_datos_generales.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pedido_detalle' => [
					['tbl_pedido_detalle.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_permiso' => [
					['tbl_permiso.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_resolucion' => [
					['tbl_resolucion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_medio_pago' => [
					['tbl_medio_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_producto' => [
					['tbl_categoria_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pedido' => [
					['tbl_pedido.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_barrio' => [
					['tbl_barrio.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_concepto_nomina' => [
					['tbl_concepto_nomina.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_cargo' => [
					['tbl_cargo.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_iva' => [
					['tbl_iva.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_compras' => [
					['tbl_compras.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_comprobante_contable' => [
					['tbl_comprobante_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_moneda' => [
					['tbl_moneda.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estrato_economico' => [
					['tbl_estrato_economico.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_novedades_nomina' => [
					['tbl_novedades_nomina.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_usuario' => [
					['tbl_usuario.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_clase_libreta_militar' => [
					['tbl_clase_libreta_militar.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_persona' => [
					['tbl_categoria_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_frecuencia' => [
					['tbl_frecuencia.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pais' => [
					['tbl_pais.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_banco' => [
					['tbl_banco.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_contrato_detalle' => [
					['tbl_contrato_detalle.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_sede' => [
					['tbl_sede.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_informacion_tributaria' => [
					['tbl_informacion_tributaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_concepto' => [
					['tbl_concepto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_area' => [
					['tbl_area.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_bodega' => [
					['tbl_bodega.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_contrato' => [
					['tbl_tipo_contrato.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_descuento' => [
					['tbl_descuento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_rh' => [
					['tbl_rh.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_mvto_inventario_detalle' => [
					['tbl_mvto_inventario_detalle.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_empresa' => [
					['tbl_empresa.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_periodo_pago' => [
					['tbl_periodo_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_especialidad' => [
					['tbl_especialidad.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_producto' => [
					['tbl_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_cuenta_contable' => [
					['tbl_categoria_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_contrato' => [
					['tbl_contrato.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_ubicacion' => [
					['tbl_ubicacion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_empleado_datos_personales' => [
					['tbl_empleado_datos_personales.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_reteiva' => [
					['tbl_reteiva.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_documento' => [
					['tbl_documento.est_intIdEstado','tbl_estado.est_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_concepto_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_concepto_nomina',
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdCidEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado_cba',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				    ),
				  ),
				  'tbl_cargo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}

