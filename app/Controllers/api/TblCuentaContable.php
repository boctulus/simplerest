<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblCuentaContable extends ApiController
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
