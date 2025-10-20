<?php 

return [
        'related_tables' => array (
  'articulo' => 
  array (
    0 => 'categoria',
    1 => 'venta_pack',
    2 => 'carrito_detalle',
    3 => 'carrito_detalle_item_extra',
    4 => 'detalle_venta',
    5 => 'empresa_producto',
  ),
  'articulo_seleccionable' => 
  array (
    0 => 'empresa_producto',
  ),
  'carrito' => 
  array (
    0 => 'cliente',
    1 => 'carrito_detalle',
    2 => 'carrito_detalle_item_extra',
  ),
  'carrito_detalle' => 
  array (
    0 => 'carrito',
    1 => 'articulo',
  ),
  'carrito_detalle_item_extra' => 
  array (
    0 => 'carrito',
    1 => 'articulo',
  ),
  'categoria' => 
  array (
    0 => 'articulo',
  ),
  'cliente' => 
  array (
    0 => 'carrito',
    1 => 'venta',
  ),
  'configuracion' => 
  array (
    0 => 'empresa_configuracion',
  ),
  'descuentosrecargos' => 
  array (
    0 => 'detalle_venta',
  ),
  'detalle_venta' => 
  array (
    0 => 'articulo',
    1 => 'venta',
    2 => 'descuentosrecargos',
  ),
  'documentodte' => 
  array (
    0 => 'venta',
  ),
  'empresa' => 
  array (
    0 => 'empresa_producto',
  ),
  'empresa_configuracion' => 
  array (
    0 => 'configuracion',
  ),
  'empresa_producto' => 
  array (
    0 => 'empresa',
    1 => 'articulo',
    2 => 'articulo_seleccionable',
  ),
  'estado_pago' => 
  array (
    0 => 'venta',
  ),
  'impuesto' => 
  array (
    0 => 'venta',
  ),
  'metodo_pago' => 
  array (
    0 => 'venta',
  ),
  'referencia' => 
  array (
    0 => 'venta',
  ),
  'rol' => 
  array (
    0 => 'usuario_rol',
  ),
  'usuario' => 
  array (
    0 => 'usuario_empresa',
    1 => 'usuario_rol',
  ),
  'usuario_empresa' => 
  array (
    0 => 'usuario',
  ),
  'usuario_rol' => 
  array (
    0 => 'rol',
    1 => 'usuario',
  ),
  'venta' => 
  array (
    0 => 'cliente',
    1 => 'documentodte',
    2 => 'estado_pago',
    3 => 'impuesto',
    4 => 'metodo_pago',
    5 => 'venta_estado',
    6 => 'detalle_venta',
    7 => 'referencia',
  ),
  'venta_estado' => 
  array (
    0 => 'venta',
  ),
  'venta_pack' => 
  array (
    0 => 'articulo',
  ),
),
        'relation_type'  => array (
  'articulo~categoria' => 'n:1',
  'articulo~venta_pack' => '1:n',
  'articulo~carrito_detalle' => '1:n',
  'articulo~carrito_detalle_item_extra' => '1:n',
  'articulo~detalle_venta' => '1:n',
  'articulo~empresa_producto' => '1:n',
  'articulo_seleccionable~empresa_producto' => 'n:1',
  'carrito~cliente' => 'n:1',
  'carrito~carrito_detalle' => '1:n',
  'carrito~carrito_detalle_item_extra' => '1:n',
  'carrito_detalle~carrito' => 'n:1',
  'carrito_detalle~articulo' => 'n:1',
  'carrito_detalle_item_extra~carrito' => 'n:1',
  'carrito_detalle_item_extra~articulo' => 'n:1',
  'categoria~articulo' => '1:n',
  'cliente~carrito' => '1:n',
  'cliente~venta' => '1:n',
  'configuracion~empresa_configuracion' => '1:n',
  'descuentosrecargos~detalle_venta' => 'n:1',
  'detalle_venta~articulo' => 'n:1',
  'detalle_venta~venta' => 'n:1',
  'detalle_venta~descuentosrecargos' => '1:n',
  'documentodte~venta' => '1:n',
  'empresa~empresa_producto' => '1:n',
  'empresa_configuracion~configuracion' => 'n:1',
  'empresa_producto~empresa' => 'n:1',
  'empresa_producto~articulo' => 'n:1',
  'empresa_producto~articulo_seleccionable' => '1:n',
  'estado_pago~venta' => '1:n',
  'impuesto~venta' => '1:n',
  'metodo_pago~venta' => '1:n',
  'referencia~venta' => 'n:1',
  'rol~usuario_rol' => '1:n',
  'usuario~usuario_empresa' => '1:n',
  'usuario~usuario_rol' => '1:n',
  'usuario_empresa~usuario' => 'n:1',
  'usuario_rol~rol' => 'n:1',
  'usuario_rol~usuario' => 'n:1',
  'venta~cliente' => 'n:1',
  'venta~documentodte' => 'n:1',
  'venta~estado_pago' => 'n:1',
  'venta~impuesto' => 'n:1',
  'venta~metodo_pago' => 'n:1',
  'venta~venta_estado' => 'n:1',
  'venta~detalle_venta' => '1:n',
  'venta~referencia' => '1:n',
  'venta_estado~venta' => '1:n',
  'venta_pack~articulo' => 'n:1',
  'articulo~carrito' => 'n:m',
  'carrito~articulo' => 'n:m',
  'articulo~venta' => 'n:m',
  'venta~articulo' => 'n:m',
  'articulo~empresa' => 'n:m',
  'empresa~articulo' => 'n:m',
  'rol~usuario' => 'n:m',
  'usuario~rol' => 'n:m',
),
        'multiplicity'   => array (
  'articulo~categoria' => false,
  'articulo~venta_pack' => true,
  'articulo~carrito_detalle' => true,
  'articulo~carrito_detalle_item_extra' => true,
  'articulo~detalle_venta' => true,
  'articulo~empresa_producto' => true,
  'articulo_seleccionable~empresa_producto' => false,
  'carrito~cliente' => false,
  'carrito~carrito_detalle' => true,
  'carrito~carrito_detalle_item_extra' => true,
  'carrito_detalle~carrito' => false,
  'carrito_detalle~articulo' => false,
  'carrito_detalle_item_extra~carrito' => false,
  'carrito_detalle_item_extra~articulo' => false,
  'categoria~articulo' => true,
  'cliente~carrito' => true,
  'cliente~venta' => true,
  'configuracion~empresa_configuracion' => true,
  'descuentosrecargos~detalle_venta' => false,
  'detalle_venta~articulo' => false,
  'detalle_venta~venta' => false,
  'detalle_venta~descuentosrecargos' => true,
  'documentodte~venta' => true,
  'empresa~empresa_producto' => true,
  'empresa_configuracion~configuracion' => false,
  'empresa_producto~empresa' => false,
  'empresa_producto~articulo' => false,
  'empresa_producto~articulo_seleccionable' => true,
  'estado_pago~venta' => true,
  'impuesto~venta' => true,
  'metodo_pago~venta' => true,
  'referencia~venta' => false,
  'rol~usuario_rol' => true,
  'usuario~usuario_empresa' => true,
  'usuario~usuario_rol' => true,
  'usuario_empresa~usuario' => false,
  'usuario_rol~rol' => false,
  'usuario_rol~usuario' => false,
  'venta~cliente' => false,
  'venta~documentodte' => false,
  'venta~estado_pago' => false,
  'venta~impuesto' => false,
  'venta~metodo_pago' => false,
  'venta~venta_estado' => false,
  'venta~detalle_venta' => true,
  'venta~referencia' => true,
  'venta_estado~venta' => true,
  'venta_pack~articulo' => false,
  'articulo~carrito' => true,
  'carrito~articulo' => true,
  'articulo~venta' => true,
  'venta~articulo' => true,
  'articulo~empresa' => true,
  'empresa~articulo' => true,
  'rol~usuario' => true,
  'usuario~rol' => true,
),
];