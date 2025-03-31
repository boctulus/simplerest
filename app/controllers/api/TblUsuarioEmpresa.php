<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class TblUsuarioEmpresa extends MyApiController
{ 
    static protected $soft_delete = true;
    
    static protected $connect_to = [
        'user_roles'
    ];

    function __construct()
    {       
        parent::__construct();
    }        
} 
