<?php

namespace simplerest\core\api\v1;

use simplerest\core\controllers\Controller;
use simplerest\core\interfaces\IAuth;


abstract class ResourceController extends Controller
{
    protected $acl;
    protected $auth;

    protected $headers = [];

    function __construct(?IAuth $auth = null)
    {   
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