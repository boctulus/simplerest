<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblUsuarioEmpresa extends ApiController
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
