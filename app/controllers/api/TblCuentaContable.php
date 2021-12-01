<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

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
