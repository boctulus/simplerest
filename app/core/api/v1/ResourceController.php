<?php

namespace simplerest\core\api\v1;

use simplerest\libs\Debug;
use simplerest\core\Request;
use simplerest\libs\Factory;
use simplerest\core\Controller;
use simplerest\core\api\v1\AuthController;
use simplerest\libs\DB;


abstract class ResourceController extends Controller
{
    protected $acl;
    protected $auth;
    protected $uid;
    protected $roles = [];
    protected $permissions = [];


    protected $headers = [
        'Access-Control-Allow-Headers' => 'Authorization,Content-Type', 
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET,POST,DELETE,PUT,PATCH,HEAD,OPTIONS',
        'Access-Control-Allow-Credentials' => 'true',
        'Content-Type' => 'application/json; charset=UTF-8'
    ];

    function __construct(object $auth = null)
    {   
        foreach ($this->headers as $key => $header){
            header("$key: $header");
        } 
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            Factory::response()->sendOK(); // no tocar !
        }
        
        $auth = $auth ?? Factory::auth();
        $this->auth = ($auth)->check();
        
        $this->uid          = $this->auth['uid']; 
        $this->roles        = $this->auth['roles'];
        $this->permissions  = $this->auth['permissions'];   
        

        $this->acl = Factory::acl();

        //dd($this->uid, 'uid');
        //dd($this->acl->getRoleName(), 'possible roles');  ///// 
        //dd($this->roles, 'active roles');
        //dd($this->permissions, 'permissions');

        parent::__construct();
    }
    
}  