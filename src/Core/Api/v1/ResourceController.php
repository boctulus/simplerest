<?php

namespace Boctulus\Simplerest\Core\Api\v1;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Interfaces\IAuth;


abstract class ResourceController extends Controller
{
    protected $acl;
    protected $auth;

    protected $headers = [];

    function __construct(?IAuth $auth = null)
    {   
        if (is_cli()){
            return;
        }

        cors(); 

        foreach ($this->headers as $key => $header){
            header("$key: $header");
        } 
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            response()->sendOK(); // no tocar !
        }
        
        $auth = $auth ?? auth();
        $this->auth = ($auth)->check();      
          
        parent::__construct();
    }
    
}

