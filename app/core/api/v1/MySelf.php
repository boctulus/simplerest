<?php

namespace simplerest\core\api\v1;

use simplerest\controllers\MyApiController; 
use simplerest\core\interfaces\IAuth;
use simplerest\libs\Factory;
use simplerest\libs\Arrays;
use simplerest\libs\DB;
use simplerest\libs\Debug;
use simplerest\libs\Url;
use simplerest\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;

class MySelf extends MyApiController 
{  
    protected $model_table = 'users';
	protected $active = 'active';

    function __construct() 
    { 
        parent::__construct();

        if (Factory::request()->authMethod() != NULL){
                $this->callable = ['get', 'put', 'patch', 'delete'];

                $this->is_listable = true;
                $this->is_retrievable = true;
        } else {
            Factory::response()->sendError("Forbidden", 403, "You need to be authenticated");
        }
    }

    function get($id = null){
        $id = $this->uid;
        parent::get($id);
    } 

    function put($id = NULL)
    { 
        $id = $this->uid;
        parent::put($id);
    } //

    function patch($id = NULL)
    { 
        $id = $this->uid;
        parent::patch($id);
    } //
        
    function onPuttingAfterCheck($id, &$data){
        $this->instance->fill([$this->active]);
    }

    function delete($id = null){
        $id = $this->uid;

        $u = DB::table($this->model_table);

        if ($u->inSchema(['active'])){
            Factory::response()->send("Account deactivation not implemented", 501);
        }

        $ok = (bool) $u
        ->where([['id', $id], [$this->active, 1]])
        ->fill([$this->active])
        ->update([$this->active => 0]);

        if ($ok) {
            Factory::response()->send("Your account was succesfully disabled");
        } else {
            Factory::response()->send("An error has ocurred trying to disable your account.");
        }        
    } // 
       
    
}  