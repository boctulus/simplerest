<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\Acl;

class UserTbPermissions extends MyApiController
{   
    //protected $model_name  = 'UserTbPermissionsModel';
    //protected $table_name = 'user_tb_permissions';

    function __construct()
    {

        // Falta limitar acceso
        $this->callable = ['post', 'put', 'get', 'put', 'patch'];

        $this->is_listable = true;
        $this->is_retrievable = true;
                
        parent::__construct();
    }

    function post(){
        $data = Factory::request()->getBody(false);

        if (empty($data))
            Factory::response()->sendError('Invalid JSON',400);
        
        $instance = DB::table('user_tb_permissions')->assoc();

        try {
            $conn = DB::getConnection();
            $instance->setConn($conn);

            if ($instance->inSchema(['created_by'])){
                $data['created_by'] = Acl::getCurrentUid();
            }

            $validado = (new Validator)->validate($instance->getRules(), $data);
            if ($validado !== true){
                Factory::response()->sendError(_('Data validation error'), 400, $validado);
            }  

            DB::transaction(function() use($data, $instance){                
                $ok = DB::table('user_tb_permissions')
                ->where(['tb' => $data['tb'], 'user_id' => $data['user_id']])
                ->delete(false);

                $id = $instance->create($data);

                Factory::response()->send(['id' => $id], 201);
            });
        

        } catch (InvalidValidationException $e) { 
            Factory::response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        } catch (\Exception $e) {
            Factory::response()->sendError($e->getMessage());
        }	

    } // 
        
} // end class
