<?php 

$pivots = array (
  'empleado,sucursal' => 'asignado',
  'modulo,usuario' => 'permiso',
);

$pivot_fks = array (
  'asignado' => 
  array (
    'empleado' => 'chr_emplcodigo',
    'sucursal' => 'chr_sucucodigo',
  ),
  'permiso' => 
  array (
    'usuario' => 'chr_emplcodigo',
    'modulo' => 'int_moducodigo',
  ),
);

$relationships = array (
  'asignado' => 
  array (
    'empleado' => 
    array (
      0 => 
      array (
        0 => 'empleado.chr_emplcodigo',
        1 => 'asignado.chr_emplcodigo',
      ),
    ),
    'sucursal' => 
    array (
      0 => 
      array (
        0 => 'sucursal.chr_sucucodigo',
        1 => 'asignado.chr_sucucodigo',
      ),
    ),
  ),
  'permiso' => 
  array (
    'usuario' => 
    array (
      0 => 
      array (
        0 => 'usuario.chr_emplcodigo',
        1 => 'permiso.chr_emplcodigo',
      ),
    ),
    'modulo' => 
    array (
      0 => 
      array (
        0 => 'modulo.int_moducodigo',
        1 => 'permiso.int_moducodigo',
      ),
    ),
  ),
);
