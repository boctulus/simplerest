<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Validator;
use Boctulus\Simplerest\Core\Exceptions\InvalidValidationException;

class UserDenyPermissions extends ApiController
{
    function __construct()
    {
        $this->callable       = ['get', 'post', 'delete'];
        $this->is_listable    = true;
        $this->is_retrievable = true;

        parent::__construct();

        if (auth()->isGuest()){
            response()->error("Not authorized", 403);
        }

        if (!acl()->hasSpecialPermission('grant')) {
            response()->error("Forbidden — requires 'grant' special permission", 403);
        }
    }

    function post()
    {
        $data = request()->getBody(false);

        if (empty($data)) {
            error('Invalid JSON', 400);
        }

        $instance = DB::table('user_deny_permissions')->assoc();

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

            DB::transaction(function() use ($data, $instance) {
                DB::table('user_deny_permissions')
                    ->where([
                        'user_id'  => $data['user_id'],
                        'resource' => $data['resource'],
                        'action'   => $data['action'],
                    ])
                    ->delete(false);

                $id = $instance->create($data);

                response()->send(['id' => $id], 201);
            });
        } catch (InvalidValidationException $e) {
            error('Validation Error', 400, json_decode($e->getMessage()));
        } catch (\Exception $e) {
            error($e->getMessage());
        }
    }
}
