<?php

namespace Boctulus\Simplerest\Core\Api;

use Boctulus\Simplerest\Core\Api\ApiController;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Libs\Debug;
use Boctulus\Simplerest\Core\Libs\Validator;
use Boctulus\Simplerest\Core\Exceptions\InvalidValidationException;
use Boctulus\Simplerest\Core\Libs\Url;


class TrashCan extends ApiController
{
    protected $model;
    protected $instance;
    protected $instance2;
    
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

        // Use get_model_name() helper to properly handle tenant/connection
        $this->model    = get_model_name($this->table_name, $this->tenantid);
        $api_ctrl = namespace_url() . '\\Controllers\\api\\' . ucfirst($entity);
        
        if (!class_exists($api_ctrl)){
            error("Entity $entity not found", 404);
        }

        if (!$api_ctrl::hasSoftDelete()){
            error('Not implemented', 501, "Trashcan not implemented for $entity");
        }
        
        if (!class_exists($this->model))
            error("Entity $entity not found", 400);

        $this->instance = (new $this->model())->assoc();
        $this->instance2 = (new $this->model())->assoc();

        if (!$this->instance->inSchema([$this->instance->deletedAt()])){
            error('Not implemented', 501, "Trashcan not implemented for $entity");
        }

        $this->ask_for_deleted = true; 
        
        //var_dump(Factory::request()->getBody());
        //dd($this->model_name);
        //exit;
        parent::__construct();

    }

    // Override to always return instances with showDeleted() enabled
    protected function getModelInstance($fetch_mode = 'ASSOC', bool $reuse = false){
        $instance = parent::getModelInstance($fetch_mode, $reuse);
        $instance->showDeleted();
        return $instance;
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

    protected function onPuttingBeforeCheck($id, &$data){
        $this->instance->showDeleted();
        $this->instance2->showDeleted();
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
