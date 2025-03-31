<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblCuentaContable extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		//'tbl_grupo_cuenta_contable',
        'tbl_sub_cuenta_contable',
        'tbl_usuario'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
