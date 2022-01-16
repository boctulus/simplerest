<?php

namespace simplerest\core\api\v1;

use simplerest\libs\Debug;
use simplerest\core\Acl;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;


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
        
        Acl::setCurrentUid($this->auth['uid']); 
        Acl::setCurrentRoles($this->auth['roles']);
        Acl::setCurrentPermissions($this->auth['permissions']);   

        $this->acl = Factory::acl();

        parent::__construct();
    }
    
}  