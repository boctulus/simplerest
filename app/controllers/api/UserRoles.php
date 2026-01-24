<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController;
use Boctulus\Simplerest\Core\Libs\Factory;


class UserRoles extends MyApiController  /* implements IApiController */
{    
    public $table_name = 'user_roles';

    static protected $connect_to = [
        'roles'
    ];

    function __construct()
    {
        if (request()->hasAuth() && auth()->isRegistered()){
            $this->callable = ['get'];

            $this->is_retrievable   = true;
            $this->is_listable      = true;
        }

        parent::__construct();
    }

    function get($id = null){
        parent::get($id);
    } 

    function onGettingAfterCheck($id){
        if (!acl()->hasSpecialPermission('grant') && 
            !acl()->hasSpecialPermission('read_all')) {
            $this->instance->where(['user_id' => $this->auth['uid']]); 
        }
    }

    function post($id = NULL)
    { 
        if (!acl()->hasSpecialPermission('grant')){
            error('Forbidden to change role', 403);
        }

        parent::post($id);
    } //
    
    function put($id = NULL)
    { 
        if (!acl()->hasSpecialPermission('grant')){
            error('Forbidden to change role', 403);
        }

        parent::put($id);
    } //

    function patch($id = NULL)
    { 
        if (!acl()->hasSpecialPermission('grant')){
            error('Forbidden to change role', 403);
        }

        parent::patch($id);
    } //


    function delete($id = NULL)
    { 
        if (!acl()->hasSpecialPermission('grant')){
            error('Forbidden to change role', 403);
        }

        parent::delete($id);
    } //
        
} // end class
