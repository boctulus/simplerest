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
				'est_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['est_intId'],

			'autoincrement' => 'est_intId',

			'nullable'		=> ['est_intId', 'est_dtimFechaCreacion', 'est_dtimFechaActualizacion', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'est_intId' => ['type' => 'int'],
				'est_varNombre' => ['type' => 'str', 'max' => 20, 'required' => true],
				'est_varIcono' => ['type' => 'str', 'max' => 100, 'required' => true],
				'est_varColor' => ['type' => 'str', 'max' => 100, 'required' => true],
				'est_dtimFechaCreacion' => ['type' => 'datetime'],
				'est_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estado.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estado.usu_intIdCreador'],
					['tbl_usuario.est_intEstado','tbl_estado.est_intId']
				],
				'tbl_rol_permiso' => [
					['tbl_rol_permiso.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_permiso' => [
					['tbl_permiso.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.est_intIdEstado_cba','tbl_estado.est_intId']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_bodega' => [
					['tbl_bodega.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_persona' => [
					['tbl_categoria_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_motivo_retiro' => [
					['tbl_motivo_retiro.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_unidadmedida' => [
					['tbl_unidadmedida.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_identificacion' => [
					['tbl_categoria_identificacion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_producto' => [
					['tbl_categoria_producto.est_intIdEstado_cap','tbl_estado.est_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pais' => [
					['tbl_pais.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_genero' => [
					['tbl_genero.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_contrato' => [
					['tbl_tipo_contrato.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_periodo_pago' => [
					['tbl_periodo_pago.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estrato_economico' => [
					['tbl_estrato_economico.est_intIdestado','tbl_estado.est_intId']
				],
				'tbl_banco' => [
					['tbl_banco.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_licencia_conduccion' => [
					['tbl_categoria_licencia_conduccion.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_producto' => [
					['tbl_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_rol' => [
					['tbl_rol.est_intIdEstado_rol','tbl_estado.est_intId']
				],
				'tbl_iva' => [
					['tbl_iva.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cargo' => [
					['tbl_cargo.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_categoria_documento' => [
					['tbl_categoria_documento.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_clase_libreta_militar' => [
					['tbl_clase_libreta_militar.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cliente_informacion_tributaria' => [
					['tbl_cliente_informacion_tributaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_arl' => [
					['tbl_arl.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_moneda' => [
					['tbl_moneda.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estado_civil' => [
					['tbl_estado_civil.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_proveedor_informacion_tributaria' => [
					['tbl_proveedor_informacion_tributaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_rh' => [
					['tbl_rh.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_centro_costos' => [
					['tbl_centro_costos.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_persona' => [
					['tbl_persona.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_empresa' => [
					['tbl_empresa.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_pension' => [
					['tbl_pension.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_estudios' => [
					['tbl_estudios.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_concepto' => [
					['tbl_concepto.est_intIdEstado','tbl_estado.est_intId']
				],
				'tbl_descuento' => [
					['tbl_descuento.est_intIdEstado','tbl_estado.est_intId']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'tbl_estado',
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
				        0 => 'tbl_estado',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    2 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'est_intEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				    ),
				  ),
				  'tbl_rol_permiso' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol_permiso',
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
				  'tbl_unidadmedida' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_unidadmedida',
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_documento',
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_identificacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_identificacion',
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
				        1 => 'est_intIdEstado_cap',
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
				  'tbl_estado_civil' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado_civil',
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
				  'tbl_rol' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'est_intIdEstado_rol',
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
				  'tbl_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa',
				        1 => 'est_intIdEstado',
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
				  'tbl_proveedor_informacion_tributaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor_informacion_tributaria',
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
				  'tbl_estrato_economico' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estrato_economico',
				        1 => 'est_intIdestado',
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
				  'tbl_pension' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pension',
				        1 => 'est_intIdEstado',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
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
				  'tbl_cliente_informacion_tributaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
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
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estado.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estado.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'tbl_estado',
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
				        0 => 'tbl_estado',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

