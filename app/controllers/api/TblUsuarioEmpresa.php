<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

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
