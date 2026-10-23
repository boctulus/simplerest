<?php

namespace Boctulus\FineGrainedACL;

use Boctulus\Simplerest\Core\Libs\DB;

class Acl extends \Boctulus\Simplerest\Core\Security\Acl
{
    // Every possible role
    protected $roles = [];  

    protected $role_perms = [];
    protected $role_ids   = [];
    protected $role_names = [];
    protected $sp_permissions = []; 
    protected $current_role = null;
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

            if (static::isRoleCatalogPersistenceDeferred()) {
                $this->roles = [];
                return;
            }

            foreach($this->roles as $rr){
                $this->role_names[] = $rr['name'];
                $this->role_ids[]   = $rr['id'];
            }
        });        
    }

    public function addRole(string $role_name, $role_id = null)
    {
        if (static::isRoleCatalogPersistenceDeferred()) {
            return parent::addRole($role_name, $role_id);
        }

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

    public function reconcileRoleCatalog(): void
    {
        if (!static::isRoleCatalogPersistenceDeferred()) {
            return;
        }

        $desired = [];
        foreach ($this->role_perms as $name => $permissions) {
            $desired[$name] = $permissions['role_id'];
        }

        $catalog = DB::transaction(function () use ($desired) {
            $persisted_by_name = [];
            $persisted_by_id = [];

            foreach (DB::table('roles')->get() as $role) {
                $name = (string) $role['name'];
                $id = (int) $role['id'];

                if (array_key_exists($name, $persisted_by_name)) {
                    throw new \RuntimeException("Cannot force ACL regeneration: persisted role name '{$name}' is not unique");
                }

                $persisted_by_name[$name] = $role;
                $persisted_by_id[$id] = $role;
            }

            foreach (DB::table('user_roles')->get() as $assignment) {
                $role_id = (int) $assignment['role_id'];
                $role_name = $persisted_by_id[$role_id]['name'] ?? null;

                if ($role_name === null || !array_key_exists($role_name, $desired)) {
                    throw new \RuntimeException(
                        "Cannot force ACL regeneration: assigned role_id '{$role_id}' has no unambiguous role-name mapping in config/acl.php"
                    );
                }
            }

            foreach ($persisted_by_name as $name => $role) {
                if (!array_key_exists($name, $desired)) {
                    throw new \RuntimeException(
                        "Cannot force ACL regeneration: persisted role '{$name}' is missing from config/acl.php; migrate application references before removing it"
                    );
                }
            }

            foreach ($desired as $name => $requested_id) {
                if (isset($persisted_by_name[$name])) {
                    $persisted_id = (int) $persisted_by_name[$name]['id'];

                    if ($requested_id !== null && (int) $requested_id !== $persisted_id) {
                        throw new \RuntimeException(
                            "Cannot force ACL regeneration: role '{$name}' has persisted id '{$persisted_id}' but config requests '{$requested_id}'; migrate application references before changing its id"
                        );
                    }

                    $desired[$name] = $persisted_by_name[$name]['id'];
                    continue;
                }

                if ($requested_id !== null && isset($persisted_by_id[(int) $requested_id])) {
                    $existing_name = $persisted_by_id[(int) $requested_id]['name'];
                    throw new \RuntimeException(
                        "Cannot force ACL regeneration: requested role id '{$requested_id}' for '{$name}' is already used by '{$existing_name}'"
                    );
                }

                $role = ['name' => $name];
                if ($requested_id !== null) {
                    $role['id'] = $requested_id;
                }

                $desired[$name] = DB::table('roles')->create($role);
            }

            return [
                'roles' => DB::table('roles')->get(),
                'desired' => $desired,
            ];
        });

        $desired = $catalog['desired'];
        $this->roles = $catalog['roles'];
        $this->role_ids = array_column($this->roles, 'id');
        $this->role_names = array_column($this->roles, 'name');

        foreach ($desired as $name => $id) {
            $this->role_perms[$name]['role_id'] = $id;
        }

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

