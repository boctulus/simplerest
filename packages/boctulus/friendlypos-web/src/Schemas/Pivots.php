<?php 

$pivots        = array (
  'articulo,carrito' => 'carrito_detalle',
  'articulo,venta' => 'detalle_venta',
  'articulo,empresa' => 'empresa_producto',
  'rol,usuario' => 'usuario_rol',
);

$pivot_fks     = array (
  'carrito_detalle_item_extra' => 
  array (
    'carrito' => 'idCarrito',
    'articulo' => 'idProducto',
  ),
  'carrito_detalle' => 
  array (
    'carrito' => 'idCarrito',
    'articulo' => 'idProducto',
  ),
  'detalle_venta' => 
  array (
    'articulo' => 'idProducto',
    'venta' => 'idVenta',
  ),
  'empresa_producto' => 
  array (
    'empresa' => 'idEmpresa',
    'articulo' => 'idProducto',
  ),
  'usuario_rol' => 
  array (
    'rol' => 'rol_idRol',
    'usuario' => 'Usuarios_idUsuarios',
  ),
);

$relationships = array (
  'carrito_detalle_item_extra' => 
  array (
    'carrito' => 
    array (
      0 => 
      array (
        0 => 'carrito.idCarrito',
        1 => 'carrito_detalle_item_extra.idCarrito',
      ),
    ),
    'articulo' => 
    array (
      0 => 
      array (
        0 => 'articulo.idProducto',
        1 => 'carrito_detalle_item_extra.idProducto',
      ),
    ),
  ),
  'carrito_detalle' => 
  array (
    'carrito' => 
    array (
      0 => 
      array (
        0 => 'carrito.idCarrito',
        1 => 'carrito_detalle.idCarrito',
      ),
    ),
    'articulo' => 
    array (
      0 => 
      array (
        0 => 'articulo.idProducto',
        1 => 'carrito_detalle.idProducto',
      ),
    ),
  ),
  'detalle_venta' => 
  array (
    'articulo' => 
    array (
      0 => 
      array (
        0 => 'articulo.idProducto',
        1 => 'detalle_venta.idProducto',
      ),
    ),
    'venta' => 
    array (
      0 => 
      array (
        0 => 'venta.idVenta',
        1 => 'detalle_venta.idVenta',
      ),
    ),
  ),
  'empresa_producto' => 
  array (
    'empresa' => 
    array (
      0 => 
      array (
        0 => 'empresa.idEmpresa',
        1 => 'empresa_producto.idEmpresa',
      ),
    ),
    'articulo' => 
    array (
      0 => 
      array (
        0 => 'articulo.idProducto',
        1 => 'empresa_producto.idProducto',
      ),
    ),
  ),
  'usuario_rol' => 
  array (
    'rol' => 
    array (
      0 => 
      array (
        0 => 'rol.idRol',
        1 => 'usuario_rol.rol_idRol',
      ),
    ),
    'usuario' => 
    array (
      0 => 
      array (
        0 => 'usuario.idUsuario',
        1 => 'usuario_rol.Usuarios_idUsuarios',
      ),
    ),
  ),
);
