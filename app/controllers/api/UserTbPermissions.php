<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\core\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\Acl;

class UserTbPermissions extends MyApiController
{   
    function __construct()
    {
        // Falta limitar acceso
        $this->callable = ['post', 'put', 'get', 'put', 'patch'];

        $this->is_listable = true;
        $this->is_retrievable = true;
                
        parent::__construct();

        if (auth()->isGuest()){
            response()->error("Not authorized", 403);
        }
    }

    function post(){
        $data = request()->getBody(false);

        if (empty($data))
            error('Invalid JSON',400);
        
        $instance = DB::table('user_tb_permissions')->assoc();

        try {
            $conn = DB::getConnection();
            $instance->setConn($conn);

            if ($instance->inSchema(['created_by'])){
                $data['created_by'] = auth()->uid();
            }

            $validado = (new Validator)->validate($instance->getRules(), $data);
            if ($validado !== true){
                error(trans('Data validation error'), 400, $validado);
            }  

            DB::transaction(function() use($data, $instance){                
                $ok = DB::table('user_tb_permissions')
                ->where(['tb' => $data['tb'], 'user_id' => $data['user_id']])
                ->delete(false);

                $id = $instance->create($data);

                response()->send(['id' => $id], 201);
            });
        

        } catch (InvalidValidationException $e) { 
            error('Validation Error', 400, json_decode($e->getMessage()));
        } catch (\Exception $e) {
            error($e->getMessage());
        }	

    } // 
        
} // end class
