<?php

namespace simplerest\core\api\v1;

use simplerest\controllers\MyApiController;
use simplerest\core\libs\Factory;
use simplerest\core\Acl;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\libs\Debug;
use simplerest\core\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\libs\Url;


class Collections extends MyApiController
{   
    // El ACL sabe cual es el listado de tablas de acceso restringido
    protected $forbidden_tables = [
        'folder_other_permissions',
        'folder_permissions',
        'folders',
        'roles',
        'user_roles',
        'sp_permissions',
        'user_sp_permissions',
        'user_tb_permissions',
    ];

    function __construct()
    {
        $this->forbidden_tables[] = $this->users_table;

        if (Factory::request()->authMethod() !== NULL){
            $this->callable = ['get', 'post', 'put', 'patch', 'delete'];

            $this->is_listable = true;
            $this->is_retrievable = true;
        }  
        
        parent::__construct();
    }

    function post() {
        $data = request()->getBodyDecoded();

        if (empty($data))
            error('Invalid JSON',400);
        
        if (empty($data['entity']))
            error('Parameter entity is required', 400);    

        if (empty($data['refs']))
            error('Parameter refs is required', 400);        

        $entity = $data['entity'];
        $refs   = $data['refs'];

        if (in_array(strtolower($entity), $this->forbidden_tables)){
            error('Forbidden', 403, "Table '". $entity . "' is not available for collections");
        }

        try {            
            $model = get_model_name($entity);

            if (!class_exists($model))
                error("Entity $entity doesn't exist", 400);
            
                      
            $id = DB::table('collections')->create([
                'entity'     => $entity,
                'refs'       => json_encode($refs),
                'belongs_to' => auth()->uid()
            ]);

            if ($id !== false){
                Factory::response()->send(['id' => $id], 201);
            }	
            else
                error("Error: creation of collection fails!");

        } catch (\Exception $e) {
            error($e->getMessage());
        }

    } // 
    
    protected function modify($id = NULL, bool $put_mode = false)
    {
        if ($id == null)
            Factory::response()->code(400)->error("Missing id");

        $data = Factory::request()->getBody(false);

        if (empty($data))
            error('Invalid JSON',400);

        try {
            
            if (!empty($data['remove']) && strtolower($data['remove']) == 'true'){
                if (DB::table('collections')->where(['id', $id])->delete()){
                    Factory::response()->sendOK();
                } else {
                    error("Colection not found",404);
                }
            } else {

                $row  = DB::table('collections')->where(['id', $id])->first();
            
                if (!$row){
                    Factory::response()->code(404)->error("Collection for id=$id doesn't exist");
                }
            
                $sp = Factory::acl()->hasSpecialPermission('write_all_collections');

                if (!$sp && $row['belongs_to'] != auth()->uid()){
                    error('Forbidden', 403, 'You are not the owner');
                }         
                               
                $entity = Strings::snakeToCamel($row['entity']);    
           
                $model_name   = ucfirst($entity) . 'Model';
                $table_name = strtolower($entity);

                $model    = 'simplerest\\models\\'. $model_name;
                $api_ctrl = '\simplerest\\controllers\\api\\' . ucfirst($entity);

                if (!class_exists($model))
                    error("Entity $entity doesn't exist", 400);     
                
                $instance = (new $model(true));      

                unset($data['refs']);

                foreach ($data as $k => $v){
                    if (strtoupper($v) == 'NULL' && $instance->isNullable($k)) 
                        $data[$k] = NULL;
                }

                if (!$sp) {
                    // Table must have 'belongs_to' 
                    if (!$instance->inSchema([$instance->belongsTo()])){
                        error(_("Collections are not available to this resource")); 
                    }
                    
                    $instance->where([$instance->belongsTo() => auth()->uid()]);
                }

                $validado = (new Validator())->setRequired($put_mode)->validate($instance->getRules(), $data);
                if ($validado !== true){
                    error(_('Data validation error'), 400, $validado);
                }   
                
                if ($instance->inSchema(['updated_by'])){
                    $data['updated_by'] = auth()->uid();
                }                

                $refs = json_decode($row['refs']);

                $affected = 0;
                DB::transaction(function() use ($instance, $refs, &$affected, $data, $id) {
                    $affected = $instance->whereIn('id', $refs)
                    ->update($data);
                    
                    DB::table('collections')->where(['id' => $id])->delete();
                });     

                Factory::response()->send(['affected_rows' => $affected]); 
            }

        } catch (\Exception $e) {
            error("Error during update for colection $id with message: {$e->getMessage()}");
        }
    }        


     /**
     * delete
     *
     * @param  mixed $id
     *
     * @return void
     */
    function delete($id = NULL) {
        if($id == NULL)
            error("Lacks id for Collection in request", 400);

        $data = Factory::request()->getBody();  

        try {            

            $row  = DB::table('collections')->where(['id', $id])->first();
            
            if (!$row){
                Factory::response()->code(404)->error("Collection for id=$id doesn't exist");
            }
            
            $sp = Factory::acl()->hasSpecialPermission('write_all_collections');

            if (!$sp && $row['belongs_to'] != auth()->uid()){
                error('Forbidden', 403, 'You are not the owner');
            }         

            $entity = Strings::snakeToCamel($row['entity']);    
           
            $model_name = ucfirst($entity) . 'Model';
            $table_name = strtolower($entity);

            $model    = 'simplerest\\models\\'. $model_name;
            $api_ctrl = '\simplerest\\controllers\\api\\' . ucfirst($entity);

            if (!class_exists($model))
                error("Entity $entity doesn't exist", 400);
                 
            $instance = (new $model(true));     

            // Table must have 'belongs_to' 
            if (!$sp) {
                $instance->where(['belongs_to' => auth()->uid()]);
            }    
            
            $refs = json_decode($row['refs']);
            $affected = 0;
            DB::transaction(function() use ($instance, $api_ctrl, $refs, $id, &$affected) {
                $affected = $instance->whereIn('id', $refs)
                ->delete();

                DB::table('collections')->where(['id' => $id])->delete();
            });   

            //echo " affected ( $affected )";

            Factory::response()->send(['affected_rows' => $affected]);  

        } catch (\Exception $e) {
            error("Error during DELETE for collection $id with message: {$e->getMessage()}");
        }

    } // 
        
        
} // end class
