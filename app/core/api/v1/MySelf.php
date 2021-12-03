<?php

namespace simplerest\core\api\v1;

use simplerest\controllers\MyApiController; 
use simplerest\core\interfaces\IAuth;
use simplerest\libs\Factory;
use simplerest\core\Acl;
use simplerest\libs\DB;
use simplerest\libs\Debug;
use simplerest\libs\Url;
use simplerest\libs\Strings;
use simplerest\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;

class MySelf extends MyApiController 
{  
    function __construct() 
    { 
        $this->config = config();
        $this->model_table = $this->config['users_table'];

        parent::__construct();

        $model = get_user_model_name();
        $this->is_active = $model::$is_active;
        $this->__id   = get_name_id($this->config['users_table']);
  
        if (Factory::request()->authMethod() != NULL){
                $this->callable = ['get', 'put', 'patch', 'delete'];

                $this->is_listable = true;
                $this->is_retrievable = true;
        } else {
            Factory::response()->sendError("Forbidden", 403, "You need to be authenticated");
        }
    }

    function get($id = null){
        $id = Acl::getCurrentUid();
        parent::get($id);
    } 

    function put($id = NULL)
    { 
        $id = Acl::getCurrentUid();
        parent::put($id);
    } //

    function patch($id = NULL)
    { 
        $id = Acl::getCurrentUid();
        parent::patch($id);
    } //
        
    function onPuttingAfterCheck($id, &$data){
        $this->instance->fill([$this->is_active]);
    }

    function delete($id = null){
        $id = Acl::getCurrentUid();

        $u = DB::table($this->model_table);

        if ($u->inSchema([$this->is_active])){
            Factory::response()->send("Account deactivation not implemented", 501);
        }

        $ok = (bool) $u
        ->where([[$this->__id, $id], [$this->is_active, 1]])
        ->fill([$this->is_active])
        ->update([$this->is_active => 0]);

        if ($ok) {
            Factory::response()->send("Your account was succesfully disabled");
        } else {
            Factory::response()->send("An error has ocurred trying to disable your account.");
        }        
    } // 
       
    
}  