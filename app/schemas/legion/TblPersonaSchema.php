<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_persona',

			'id_name'		=> 'per_intId',

			'attr_types'	=> [
				'per_intId' => 'INT',
				'per_varIdentificacion' => 'STR',
				'per_varDV' => 'STR',
				'per_varRazonSocial' => 'STR',
				'per_varNombre' => 'STR',
				'per_varNombre2' => 'STR',
				'per_varApellido' => 'STR',
				'per_varApellido2' => 'STR',
				'per_varNombreCompleto' => 'STR',
				'per_varDireccion' => 'STR',
				'per_varCelular' => 'STR',
				'per_varTelefono' => 'STR',
				'per_varEmail' => 'STR',
				'per_varMatriculaMercantil' => 'STR',
				'per_datFechaNacimiento' => 'STR',
				'per_dtimFechaCreacion' => 'STR',
				'per_dtimFechaActualizacion' => 'STR',
				'tpr_intIdTipoPersona' => 'INT',
				'pai_intIdPaisNacimiento' => 'INT',
				'ciu_intIdCiudadNacimiento' => 'INT',
				'dep_intIdDepartamentoNacimiento' => 'INT',
				'gen_intIdGenero' => 'INT',
				'tid_intIdTipoDocumento' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['per_intId'],

			'autoincrement' => 'per_intId',

			'nullable'		=> ['per_intId', 'per_varRazonSocial', 'per_varNombre', 'per_varNombre2', 'per_varApellido', 'per_varApellido2', 'per_varTelefono', 'per_varMatriculaMercantil', 'per_dtimFechaCreacion', 'per_dtimFechaActualizacion', 'pai_intIdPaisNacimiento', 'ciu_intIdCiudadNacimiento', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'per_intId' => ['type' => 'int'],
				'per_varIdentificacion' => ['type' => 'str', 'max' => 20, 'required' => true],
				'per_varDV' => ['type' => 'str', 'max' => 1, 'required' => true],
				'per_varRazonSocial' => ['type' => 'str', 'max' => 200],
				'per_varNombre' => ['type' => 'str', 'max' => 100],
				'per_varNombre2' => ['type' => 'str', 'max' => 100],
				'per_varApellido' => ['type' => 'str', 'max' => 100],
				'per_varApellido2' => ['type' => 'str', 'max' => 100],
				'per_varNombreCompleto' => ['type' => 'str', 'required' => true],
				'per_varDireccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'per_varCelular' => ['type' => 'str', 'max' => 15, 'required' => true],
				'per_varTelefono' => ['type' => 'str', 'max' => 15],
				'per_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'per_varMatriculaMercantil' => ['type' => 'str', 'max' => 100],
				'per_datFechaNacimiento' => ['type' => 'date', 'required' => true],
				'per_dtimFechaCreacion' => ['type' => 'datetime'],
				'per_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tpr_intIdTipoPersona' => ['type' => 'int', 'required' => true],
				'pai_intIdPaisNacimiento' => ['type' => 'int'],
				'ciu_intIdCiudadNacimiento' => ['type' => 'int'],
				'dep_intIdDepartamentoNacimiento' => ['type' => 'int', 'required' => true],
				'gen_intIdGenero' => ['type' => 'int', 'required' => true],
				'tid_intIdTipoDocumento' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['ciu_intIdCiudadNacimiento', 'dep_intIdDepartamentoNacimiento', 'est_intIdEstado', 'gen_intIdGenero', 'pai_intIdPaisNacimiento', 'tid_intIdTipoDocumento', 'tpr_intIdTipoPersona', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_persona.ciu_intIdCiudadNacimiento']
				],
				'tbl_departamento' => [
					['tbl_departamento.dep_intId','tbl_persona.dep_intIdDepartamentoNacimiento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_persona.est_intIdEstado']
				],
				'tbl_genero' => [
					['tbl_genero.gen_intId','tbl_persona.gen_intIdGenero']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_persona.pai_intIdPaisNacimiento']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.tid_intId','tbl_persona.tid_intIdTipoDocumento']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.tpr_intId','tbl_persona.tpr_intIdTipoPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_persona.usu_intIdCreador']
				],
				'tbl_empleado_informacion_pago' => [
					['tbl_empleado_informacion_pago.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_nota_credito_detalle' => [
					['tbl_nota_credito_detalle.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_cotizacion' => [
					['tbl_cotizacion.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_orden_compra_detalle' => [
					['tbl_orden_compra_detalle.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_empleado_datos_personales' => [
					['tbl_empleado_datos_personales.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_compras_detalle' => [
					['tbl_compras_detalle.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_categoria_persona_persona' => [
					['tbl_categoria_persona_persona.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_mvto_inventario_detalle' => [
					['tbl_mvto_inventario_detalle.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_compras' => [
					['tbl_compras.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_empleado_datos_generales' => [
					['tbl_empleado_datos_generales.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_contrato_empleado' => [
					['tbl_contrato_empleado.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_informacion_tributaria' => [
					['tbl_informacion_tributaria.per_intIdpersona','tbl_persona.per_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_pedido' => [
					['tbl_pedido.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_nota_debito_detalle' => [
					['tbl_nota_debito_detalle.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_factura' => [
					['tbl_factura.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.per_intIdPersona','tbl_persona.per_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'ciu_intIdCiudadNacimiento',
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
				        1 => 'dep_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'dep_intIdDepartamentoNacimiento',
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
				        0 => 'tbl_persona',
				        1 => 'est_intIdEstado',
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
				        1 => 'gen_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'gen_intIdGenero',
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
				        1 => 'pai_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'pai_intIdPaisNacimiento',
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
				        0 => 'tbl_persona',
				        1 => 'tid_intIdTipoDocumento',
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
				        1 => 'tpr_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'tpr_intIdTipoPersona',
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
				        0 => 'tbl_persona',
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
				        0 => 'tbl_persona',
				        1 => 'usu_intIdCreador',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				    ),
				  ),
				  'tbl_categoria_persona_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_persona_persona',
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdpersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_persona.ciu_intIdCiudadNacimiento']
				],
				'tbl_departamento' => [
					['tbl_departamento.dep_intId','tbl_persona.dep_intIdDepartamentoNacimiento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_persona.est_intIdEstado']
				],
				'tbl_genero' => [
					['tbl_genero.gen_intId','tbl_persona.gen_intIdGenero']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_persona.pai_intIdPaisNacimiento']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.tid_intId','tbl_persona.tid_intIdTipoDocumento']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.tpr_intId','tbl_persona.tpr_intIdTipoPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_persona.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'ciu_intIdCiudadNacimiento',
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
				        1 => 'dep_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'dep_intIdDepartamentoNacimiento',
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
				        0 => 'tbl_persona',
				        1 => 'est_intIdEstado',
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
				        1 => 'gen_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'gen_intIdGenero',
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
				        1 => 'pai_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'pai_intIdPaisNacimiento',
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
				        0 => 'tbl_persona',
				        1 => 'tid_intIdTipoDocumento',
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
				        1 => 'tpr_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'tpr_intIdTipoPersona',
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
				        0 => 'tbl_persona',
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
				        0 => 'tbl_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

