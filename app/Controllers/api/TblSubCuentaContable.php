<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblSubCuentaContable extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'tbl_cuenta_contable',
        'tbl_estado',
        'tbl_moneda',
        'tbl_usuario',
        'tbl_proveedor_informacion_tributaria',
        'tbl_iva_cuentacontable',
        'tbl_cliente_informacion_tributaria',
        'tbl_producto', //
        'tbl_iva',
        'tbl_retencion_cuentacontable',
        'tbl_banco'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
