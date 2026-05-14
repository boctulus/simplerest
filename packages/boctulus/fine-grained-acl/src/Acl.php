<?php

namespace Boctulus\FineGrainedACL;

use Boctulus\Simplerest\Core\Libs\DB;

class Acl extends \Boctulus\Simplerest\Core\Acl
{
    // Every possible role
    protected $roles = [];  

    protected $role_perms = [];
    protected $role_ids   = [];
    protected $role_names = [];
    protected $sp_permissions = []; 
    protected $current_role;
    protected $guest_name = 'guest';


    public function __construct() {
        parent::__construct();
        $this->setup();
    }

 
    protected function setup()
    {        
        withDefaultConnection(function(){
            // get all available sp_permissions
            $this->sp_permissions = DB::table('sp_permissions')->pluck('name');

            if (empty($this->sp_permissions)){
                throw new \Exception("Unexpected empty table `sp_permissions`");
            }

            // get all available roles
            $this->roles = DB::table('roles')->get();

            foreach($this->roles as $rr){
                $this->role_names[] = $rr['name'];
                $this->role_ids[]   = $rr['id'];
            }
        });        
    }

    public function addRole(string $role_name, $role_id = null)
    {
        $create = true;

        if (in_array($role_id, $this->role_ids)) {
            $create = false;

            foreach ($this->roles as $rr) {
                if ($rr['id'] == $role_id && $rr['name'] != $role_name) {
                    throw new \Exception("Role id '$role_id' can not be repeated. Trying to assign to '$role_name' but it was used for '{$rr['name']}' and it should be UNIQUE.");
                }
            }
        }

        if (in_array($role_name, $this->role_names)) {
            $create = false;

            foreach ($this->roles as $rr) {
                if ($rr['id'] != $role_id && $rr['name'] == $role_name && $role_id !== null) {
                    throw new \Exception("Role name '$role_name' can not be repeated. Trying to assign to id '$role_id' but it was used for '{$rr['id']}' and it should be UNIQUE.");
                }
            }
        }

        if ($create) {
            $role_id = DB::table('roles')->create([
                'id'   => $role_id,
                'name' => $role_name,
            ]);
        }

        $this->registerRoleState($role_name, $role_id);

        return $this;
    }

    public function getFreshTbPermissions(string $table = null, bool $unpacked = true){
        $rows = DB::table("user_tb_permissions")
        ->when(!is_null($table), function($o) use($table){
            $o->where(['tb' => $table]);
        })
        ->get();

        return $rows;        
    }

    public function getFreshSpPermissions(){
        $rows = DB::table("user_sp_permissions")
        ->get();

        return $rows;
    }

    /**
     * Fresh DB read of user_deny_permissions (business-level explicit denies).
     * Shape: ['resource' => ['action' => true]]
     */
    public function getFreshDenyPermissions($uid = null): array
    {
        return withDefaultConnection(function() use ($uid) {
            $q = DB::table('user_deny_permissions');
            if ($uid !== null) {
                $q->where(['user_id' => $uid]);
            }
            $rows = $q->get();

            $out = [];
            foreach ($rows as $r) {
                $resource = $r['resource'] ?? null;
                $action   = $r['action']   ?? null;
                if ($resource === null || $action === null) {
                    continue;
                }
                $out[$resource][$action] = true;
            }
            return $out;
        });
    }

    /**
     * Override of base Acl hook: load explicit deny rows for a specific user.
     */
    protected function fetchUserDenyPerms($uid, bool $is_auth): array
    {
        if ($uid === null) {
            return [];
        }
        return $this->getFreshDenyPermissions($uid);
    }
}

