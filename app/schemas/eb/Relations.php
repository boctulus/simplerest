<?php 

return [
        'relation_type'=> array (
  'asignado~empleado' => 'n:1',
  'asignado~sucursal' => 'n:1',
  'cargomantenimiento~moneda' => '1:1',
  'cliente~cuenta' => '1:n',
  'costomovimiento~moneda' => '1:1',
  'cuenta~cliente' => 'n:1',
  'cuenta~empleado' => 'n:1',
  'cuenta~moneda' => 'n:1',
  'cuenta~sucursal' => 'n:1',
  'cuenta~movimiento' => '1:n',
  'empleado~asignado' => '1:n',
  'empleado~cuenta' => '1:n',
  'empleado~logsession' => '1:n',
  'empleado~movimiento' => '1:n',
  'empleado~usuario' => '1:1',
  'interesmensual~moneda' => '1:1',
  'logsession~empleado' => 'n:1',
  'modulo~permiso' => '1:n',
  'moneda~cargomantenimiento' => '1:1',
  'moneda~costomovimiento' => '1:1',
  'moneda~cuenta' => '1:n',
  'moneda~interesmensual' => '1:1',
  'movimiento~cuenta' => 'n:1',
  'movimiento~empleado' => 'n:1',
  'movimiento~tipomovimiento' => 'n:1',
  'permiso~usuario' => 'n:1',
  'permiso~modulo' => 'n:1',
  'sucursal~asignado' => '1:n',
  'sucursal~cuenta' => '1:n',
  'tipomovimiento~movimiento' => '1:n',
  'usuario~empleado' => '1:1',
  'usuario~permiso' => 'n:1',
  'empleado~sucursal' => 'n:m',
  'sucursal~empleado' => 'n:m',
  'modulo~usuario' => 'n:m',
  'usuario~modulo' => 'n:m',
),
        'multiplicity' => array (
  'asignado~empleado' => false,
  'asignado~sucursal' => false,
  'cargomantenimiento~moneda' => false,
  'cliente~cuenta' => true,
  'costomovimiento~moneda' => false,
  'cuenta~cliente' => false,
  'cuenta~empleado' => false,
  'cuenta~moneda' => false,
  'cuenta~sucursal' => false,
  'cuenta~movimiento' => true,
  'empleado~asignado' => true,
  'empleado~cuenta' => true,
  'empleado~logsession' => true,
  'empleado~movimiento' => true,
  'empleado~usuario' => false,
  'interesmensual~moneda' => false,
  'logsession~empleado' => false,
  'modulo~permiso' => true,
  'moneda~cargomantenimiento' => false,
  'moneda~costomovimiento' => false,
  'moneda~cuenta' => true,
  'moneda~interesmensual' => false,
  'movimiento~cuenta' => false,
  'movimiento~empleado' => false,
  'movimiento~tipomovimiento' => false,
  'permiso~usuario' => false,
  'permiso~modulo' => false,
  'sucursal~asignado' => true,
  'sucursal~cuenta' => true,
  'tipomovimiento~movimiento' => true,
  'usuario~empleado' => false,
  'usuario~permiso' => false,
  'empleado~sucursal' => true,
  'sucursal~empleado' => true,
  'modulo~usuario' => true,
  'usuario~modulo' => true,
)
        ];