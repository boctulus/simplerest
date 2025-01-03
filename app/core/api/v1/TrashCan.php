<?php

namespace simplerest\core\api\v1;

use simplerest\controllers\MyApiController;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\libs\Debug;
use simplerest\core\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\libs\Url;


class TrashCan extends MyApiController
{
    protected $model;
    
    function __construct()
    {
        $entity  =  Factory::request()->header('entity') ??        
                    Factory::request()->shiftQuery('entity') ??
                    Factory::request()->shiftBodyParam('entity');
                
        if (empty($entity))
            error('Entity is required', 400);

        $entity = Strings::snakeToCamel($entity);

        $this->model_name = ucfirst($entity) . 'Model';
        $this->table_name = strtolower($entity);

        $this->model    = 'simplerest\\models\\'. $this->model_name;
        $api_ctrl = '\simplerest\\controllers\\api\\' . ucfirst($entity);
        
        if (!class_exists($api_ctrl)){
            error("Entity $entity not found", 404);
        }

        if (!$api_ctrl::hasSoftDelete()){
            error('Not implemented', 501, "Trashcan not implemented for $entity");
        }
        
        if (!class_exists($this->model))
            error("Entity $entity not found", 400);

        $this->instance = (new $this->model())->assoc();  
        
        if (!$this->instance->inSchema([$this->instance->deletedAt()])){
            error('Not implemented', 501, "Trashcan not implemented for $entity");
        }
            
        $this->ask_for_deleted = true; 
        
        //var_dump(Factory::request()->getBody());
        //dd($this->model_name);
        //exit;
        parent::__construct();

    }

    function get($id = null) {
        if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('read_all_trashcan')){
            error("Forbidden", 403);
        }

        parent::get($id);
    }  

    protected function onGettingAfterCheck($id){
        $this->instance
        ->showDeleted()
        ->where([$this->instance->deletedAt(), NULL, 'IS NOT']);
    }


    function post() {
        error('You can not create a trashcan resource',405);
    }        

    function modify($id = NULL, bool $put_mode = false)
    {
        if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('write_all_trashcan')){
            error("Forbidden", 403);
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
        if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('write_all_trashcan')){
            error("Forbidden", 403);
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
