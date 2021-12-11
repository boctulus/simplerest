<?php 

$pivots = array (
  'tbl_estado,tbl_usuario' => 'tbl_bodega',
  'tbl_categoria_persona,tbl_persona' => 'tbl_categoria_persona_persona',
  'tbl_transaccion,tbl_usuario' => 'tbl_documento',
);

$pivot_fks = array (
  'tbl_motivo_retiro' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_rh' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_genero' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_operador_pila' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_tipo_contrato' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_estrato_economico' => 
  array (
    'tbl_estado' => 'est_intIdestado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_categoria_identificacion' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_rol' => 
  array (
    'tbl_estado' => 'est_intIdEstado_rol',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_cuenta_contable' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_unidadmedida' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_categoria_licencia_conduccion' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_estudios' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_tipo_documento' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_categoria_documento' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_descuento' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_categoria_producto' => 
  array (
    'tbl_estado' => 'est_intIdEstado_cap',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_categoria_persona_persona' => 
  array (
    'tbl_categoria_persona' => 'cap_intIdCategoriaPersona',
    'tbl_persona' => 'per_intIdPersona',
  ),
  'tbl_cargo' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_concepto' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_arl' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_pension' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_periodo_pago' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_centro_costos' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_documento' => 
  array (
    'tbl_transaccion' => 'tra_intIdTransaccion',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_clase_libreta_militar' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_tipo_cuenta_bancaria' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_dias_pago' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_moneda' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_permiso' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_categoria_cuenta_bancaria' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_categoria_persona' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_estado_civil' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_bodega' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
);

$relationships = array (
  'tbl_motivo_retiro' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_motivo_retiro.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_motivo_retiro.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_motivo_retiro.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_rh' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_rh.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_rh.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_rh.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_genero' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_genero.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_genero.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_genero.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_operador_pila' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_operador_pila.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_operador_pila.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_operador_pila.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_tipo_contrato' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_tipo_contrato.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_contrato.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_contrato.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_estrato_economico' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_estrato_economico.est_intIdestado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_estrato_economico.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_estrato_economico.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_categoria_identificacion' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_identificacion.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_identificacion.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_identificacion.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_rol' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_rol.est_intIdEstado_rol',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_rol.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_rol.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_cuenta_contable' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_cuenta_contable.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_cuenta_contable.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_cuenta_contable.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_unidadmedida' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_unidadmedida.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_unidadmedida.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_unidadmedida.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_categoria_licencia_conduccion' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_licencia_conduccion.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_licencia_conduccion.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_licencia_conduccion.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_estudios' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_estudios.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_estudios.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_estudios.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_tipo_documento' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_tipo_documento.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_documento.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_documento.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_categoria_documento' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_documento.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_documento.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_documento.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_descuento' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_descuento.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_descuento.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_descuento.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_categoria_producto' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_producto.est_intIdEstado_cap',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_producto.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_producto.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_categoria_persona_persona' => 
  array (
    'tbl_categoria_persona' => 
    array (
      0 => 
      array (
        0 => 'tbl_categoria_persona.cap_intId',
        1 => 'tbl_categoria_persona_persona.cap_intIdCategoriaPersona',
      ),
    ),
    'tbl_persona' => 
    array (
      0 => 
      array (
        0 => 'tbl_persona.per_intId',
        1 => 'tbl_categoria_persona_persona.per_intIdPersona',
      ),
    ),
  ),
  'tbl_cargo' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_cargo.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_cargo.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_cargo.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_concepto' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_concepto.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_concepto.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_concepto.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_arl' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_arl.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_arl.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_arl.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_pension' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_pension.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_pension.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_pension.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_periodo_pago' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_periodo_pago.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_periodo_pago.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_periodo_pago.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_centro_costos' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_centro_costos.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_centro_costos.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_centro_costos.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_documento' => 
  array (
    'tbl_transaccion' => 
    array (
      0 => 
      array (
        0 => 'tbl_transaccion.tra_intId',
        1 => 'tbl_documento.tra_intIdTransaccion',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_documento.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_documento.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_clase_libreta_militar' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_clase_libreta_militar.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_clase_libreta_militar.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_clase_libreta_militar.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_tipo_cuenta_bancaria' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_tipo_cuenta_bancaria.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_cuenta_bancaria.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_cuenta_bancaria.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_dias_pago' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_dias_pago.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_dias_pago.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_dias_pago.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_moneda' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_moneda.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_moneda.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_moneda.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_permiso' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_permiso.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_permiso.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_permiso.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_categoria_cuenta_bancaria' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_cuenta_bancaria.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_cuenta_bancaria.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_cuenta_bancaria.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_categoria_persona' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_persona.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_persona.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_persona.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_estado_civil' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_estado_civil.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_estado_civil.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_estado_civil.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_bodega' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_bodega.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_bodega.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_bodega.usu_intIdCreador',
      ),
    ),
  ),
);
