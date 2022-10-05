<?php

namespace simplerest\core\api\v1;

use simplerest\core\controllers\Controller;
use simplerest\core\interfaces\IAuth;


abstract class ResourceController extends Controller
{
    protected $acl;
    protected $auth;

    protected $headers = [
        'Access-Control-Allow-Headers' => 'Authorization,Content-Type', 
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET,POST,DELETE,PUT,PATCH,HEAD,OPTIONS',
        'Access-Control-Allow-Credentials' => 'true'
    ];

    function __construct(?IAuth $auth = null)
    {   
        foreach ($this->headers as $key => $header){
            header("$key: $header");
        } 
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            response()->sendOK(); // no tocar !
        }
        
        $auth = $auth ?? auth();
        $this->auth = ($auth)->check();

        $uid   = $this->auth['uid'];
        $roles = $this->auth['roles'];
        $perms = $this->auth['permissions'];

        $auth->setCurrentUid($uid); 
        $auth->setCurrentRoles($roles);
        $auth->setCurrentPermissions($perms);   

        parent::__construct();
    }
    
}  