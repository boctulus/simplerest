<?php

namespace Boctulus\Simplerest\Core\Api\v1;

use Boctulus\Simplerest\Controllers\MyApiController; 
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Exceptions\InvalidValidationException;

class MySelf extends MyApiController 
{  
    protected $is_active;
    protected $__id;

    function __construct() 
    { 
        $config = Config::get();
        $this->table_name = $config['users_table'];

        parent::__construct();

        $model = get_user_model_name();

        $this->is_active = $model::$is_active;
        $this->__id      = get_id_name($config['users_table']);
  
        if (Factory::request()->authMethod() != NULL){
                $this->callable = ['get', 'put', 'patch', 'delete'];

                $this->is_listable = true;
                $this->is_retrievable = true;
        } else {
            error("Forbidden", 403, "You need to be authenticated");
        }
    }

    function get($id = null){
        $id = auth()->uid();
        parent::get($id);
    } 

    function put($id = NULL)
    { 
        $id = auth()->uid();
        parent::put($id);
    } //

    function patch($id = NULL)
    { 
        $id = auth()->uid();
        parent::patch($id);
    } //
        
    function onPuttingAfterCheck($id, &$data){
        $this->instance->fill([$this->is_active]);
    }

    function delete($id = null){
        $id = auth()->uid();

        $u = DB::table($this->table_name);

        if ($u->inSchema([$this->is_active])){
            response()->send("Account deactivation not implemented", 501);
        }

        $ok = (bool) $u
        ->where([[$this->__id, $id], [$this->is_active, 1]])
        ->fill([$this->is_active])
        ->update([$this->is_active => 0]);

        if ($ok) {
            response()->send("Your account was succesfully disabled");
        } else {
            response()->send("An error has ocurred trying to disable your account.");
        }        
    } // 
       
    
}  