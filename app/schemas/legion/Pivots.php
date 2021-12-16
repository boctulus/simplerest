<?php 

$pivots = array (
  'tbl_estado,tbl_usuario' => 'tbl_frecuencia',
  'tbl_naturaleza_cuenta_contable,tbl_usuario' => 'tbl_clase_cuenta_contable',
  'tbl_clase_cuenta_contable,tbl_usuario' => 'tbl_grupo_cuenta_contable',
);

$pivot_fks = array (
  'tbl_grupo_empleado' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_motivo_retiro' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_tipo_persona' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_unidad_medida' => 
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
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_operador_pila' => 
  array (
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_tipo_contrato' => 
  array (
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_estrato_economico' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_eps' => 
  array (
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_forma_de_pago' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_resolucion' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_transaccion' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_categoria_cuenta_contable' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_especialidad' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_ubicacion' => 
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
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_clase_cuenta_contable' => 
  array (
    'tbl_naturaleza_cuenta_contable' => 'nat_intId',
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
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_grupo_cuenta_contable' => 
  array (
    'tbl_clase_cuenta_contable' => 'cla_intIdClase',
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
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_categoria_producto' => 
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
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
    ),
  ),
  'tbl_pension' => 
  array (
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_naturaleza_cuenta_contable' => 
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
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
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
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_permiso' => 
  array (
    'tbl_estado' => 'est_intIdEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdActualizador',
      1 => 'usu_intIdCreador',
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
  'tbl_concepto_nomina' => 
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
    'tbl_estado' => 'est_intEstado',
    'tbl_usuario' => 
    array (
      0 => 'usu_intIdCreador',
      1 => 'usu_intIdActualizador',
    ),
  ),
  'tbl_medio_pago' => 
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
  'tbl_frecuencia' => 
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
  'tbl_grupo_empleado' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_grupo_empleado.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_grupo_empleado.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_grupo_empleado.usu_intIdActualizador',
      ),
    ),
  ),
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
  'tbl_tipo_persona' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_tipo_persona.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_persona.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_persona.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_unidad_medida' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_unidad_medida.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_unidad_medida.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_unidad_medida.usu_intIdCreador',
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
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_genero.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_genero.usu_intIdActualizador',
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
        1 => 'tbl_operador_pila.est_intEstado',
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
        1 => 'tbl_tipo_contrato.est_intEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_contrato.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_contrato.usu_intIdActualizador',
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
        1 => 'tbl_estrato_economico.est_intIdEstado',
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
  'tbl_eps' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_eps.est_intEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_eps.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_eps.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_forma_de_pago' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_forma_de_pago.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_forma_de_pago.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_forma_de_pago.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_resolucion' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_resolucion.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_resolucion.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_resolucion.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_transaccion' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_transaccion.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_transaccion.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_transaccion.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_categoria_cuenta_contable' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_categoria_cuenta_contable.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_categoria_cuenta_contable.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_categoria_cuenta_contable.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_especialidad' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_especialidad.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_especialidad.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_especialidad.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_ubicacion' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_ubicacion.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_ubicacion.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_ubicacion.usu_intIdCreador',
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
        1 => 'tbl_estudios.est_intEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_estudios.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_estudios.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_clase_cuenta_contable' => 
  array (
    'tbl_naturaleza_cuenta_contable' => 
    array (
      0 => 
      array (
        0 => 'tbl_naturaleza_cuenta_contable.ncc_intId',
        1 => 'tbl_clase_cuenta_contable.nat_intId',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_clase_cuenta_contable.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_clase_cuenta_contable.usu_intIdCreador',
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
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_documento.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_documento.usu_intIdCreador',
      ),
    ),
  ),
  'tbl_grupo_cuenta_contable' => 
  array (
    'tbl_clase_cuenta_contable' => 
    array (
      0 => 
      array (
        0 => 'tbl_clase_cuenta_contable.cla_intId',
        1 => 'tbl_grupo_cuenta_contable.cla_intIdClase',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_grupo_cuenta_contable.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_grupo_cuenta_contable.usu_intIdCreador',
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
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_descuento.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_descuento.usu_intIdCreador',
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
        1 => 'tbl_categoria_producto.est_intIdEstado',
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
        1 => 'tbl_arl.est_intEstado',
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
        1 => 'tbl_pension.est_intEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_pension.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_pension.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_naturaleza_cuenta_contable' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_naturaleza_cuenta_contable.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_naturaleza_cuenta_contable.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_naturaleza_cuenta_contable.usu_intIdCreador',
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
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_centro_costos.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_centro_costos.usu_intIdCreador',
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
        1 => 'tbl_tipo_cuenta_bancaria.est_intEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_tipo_cuenta_bancaria.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_tipo_cuenta_bancaria.usu_intIdActualizador',
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
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_moneda.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_moneda.usu_intIdActualizador',
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
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_permiso.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_permiso.usu_intIdCreador',
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
  'tbl_concepto_nomina' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_concepto_nomina.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_concepto_nomina.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_concepto_nomina.usu_intIdCreador',
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
        1 => 'tbl_estado_civil.est_intEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_estado_civil.usu_intIdCreador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_estado_civil.usu_intIdActualizador',
      ),
    ),
  ),
  'tbl_medio_pago' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_medio_pago.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_medio_pago.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_medio_pago.usu_intIdCreador',
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
  'tbl_frecuencia' => 
  array (
    'tbl_estado' => 
    array (
      0 => 
      array (
        0 => 'tbl_estado.est_intId',
        1 => 'tbl_frecuencia.est_intIdEstado',
      ),
    ),
    'tbl_usuario' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario|__usu_intIdActualizador.usu_intId',
        1 => 'tbl_frecuencia.usu_intIdActualizador',
      ),
      1 => 
      array (
        0 => 'tbl_usuario|__usu_intIdCreador.usu_intId',
        1 => 'tbl_frecuencia.usu_intIdCreador',
      ),
    ),
  ),
);
