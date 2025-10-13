<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblEstadoCivil extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        'tbl_estado',
        'tbl_usuario'
    ];

    function __construct()
    {       
        parent::__construct();
    }       
} 
