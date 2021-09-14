<?php

namespace simplerest\core\api\v1;

use simplerest\controllers\MyApiController;
use simplerest\libs\Factory;
use simplerest\libs\Arrays;
use simplerest\libs\Strings;
use simplerest\libs\DB;
use simplerest\libs\Debug;
use simplerest\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\libs\Url;


class TrashCan extends MyApiController
{
    protected $model;
    
    function __construct()
    {
        $entity  =  Factory::request()->header('entity') ??        
                    Factory::request()->shiftQuery('entity') ??
                    Factory::request()->shiftBodyParam('entity');
                
        if (empty($entity))
            Factory::response()->sendError('Entity is required', 400);

        $entity = Strings::snakeToCamel($entity);

        $this->model_name = ucfirst($entity) . 'Model';
        $this->model_table = strtolower($entity);

        $this->model    = 'simplerest\\models\\'. $this->model_name;
        $api_ctrl = '\simplerest\\controllers\\api\\' . ucfirst($entity);
        
        if (!class_exists($api_ctrl)){
            Factory::response()->sendError("Entity $entity not found", 404);
        }

        if (!$api_ctrl::hasSoftDelete()){
            Factory::response()->sendError('Not implemented', 501, "Trashcan not implemented for $entity");
        }
        
        if (!class_exists($this->model))
            Factory::response()->sendError("Entity $entity does not exists", 400);

        $this->instance = (new $this->model())->assoc();  
        
        if (!$this->instance->inSchema([$this->instance->deletedAt()])){
            Factory::response()->sendError('Not implemented', 501, "Trashcan not implemented for $entity");
        }
            
        $this->ask_for_deleted = true; 
        
        //var_dump(Factory::request()->getBody());
        //dd($this->model_name);
        //exit;
        parent::__construct();

    }

    function get($id = null) {
        if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('read_all_trashcan', $this->roles)){
            Factory::response()->sendError("Forbidden", 403);
        }

        parent::get($id);
    }  

    protected function onGettingAfterCheck($id){
        $this->instance
        ->showDeleted()
        ->where([$this->instance->deletedAt(), NULL, 'IS NOT']);
    }


    function post() {
        Factory::response()->sendError('You can not create a trashcan resource',405);
    }        

    function modify($id = NULL, bool $put_mode = false)
    {
        if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('write_all_trashcan', $this->roles)){
            Factory::response()->sendError("Forbidden", 403);
        }

        parent::modify($id, $put_mode);
    }   

    protected function onPuttingBeforeCheck2($id, &$data){
        $this->instance
        ->showDeleted()
        ->fill([$this->instance->deletedAt()]);
 
        $this->instance2
        ->showDeleted()
        ->where([$this->instance->deletedAt(), NULL, 'IS NOT']);
    }

            
    protected function onPuttingAfterCheck($id, &$data) { 
        $trashed = $data['trashed'] ?? true;
        
        if ($trashed !== false && $trashed !== 'false')
            return;
        
        unset($data['trashed']);
        $data[$this->instance->deletedAt()] = NULL;
    } 


    function delete($id = NULL) {
        if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('write_all_trashcan', $this->roles)){
            Factory::response()->sendError("Forbidden", 403);
        }

        parent::delete($id);
    } 

    protected function onDeletingBeforeCheck($id){
        $this->instance
        ->showDeleted()
        ->where([$this->instance->deletedAt(), NULL, 'IS NOT']);
    }

    protected function onDeletingAfterCheck($id){
        $this->instance
        ->setSoftDelete(false);
    }

        
} // end class
