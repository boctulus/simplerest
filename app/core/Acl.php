<?php

namespace simplerest\core;

use simplerest\core\interfaces\IAcl;
use simplerest\libs\DB;
use simplerest\libs\Factory;

abstract class Acl implements IAcl
{
    protected $role_ids   = [];
    protected $role_names = [];
    protected $role_perms = [];
    protected $current_role;
    protected $guest_name = 'guest';
    protected $sp_permissions = [];    


    public function __construct()
    {
        $this->sp_permissions = [
            'read_all',
            'write_all',
            'read_all_folders',
            'write_all_folders',
            'read_all_trashcan',
            'write_all_trashcan',
            'lock',
            'transfer',
            'impersonate',
            'fill_all',
            'grant'
        ]; 
    }

    public function addRole(string $role_name, $role_id = null) {
       
        if (in_array($role_id, $this->role_ids)){
            throw new \Exception("Role id '$role_id' can not be repetead. It should be UNIQUE.");
        }

        if (in_array($role_name, $this->role_names)){
            throw new \Exception("Role name '$role_name' can not be repetead. It should be UNIQUE.");
        }

        if ($role_name == 'guest'){
            $this->guest_name = 'guest';
        }

        $this->role_ids[]   = $role_id;
        $this->role_names[] = $role_name;
        
        $this->role_perms[$role_name] = [
                            'role_id' => $role_id,
                            'sp_permissions' => [],
                            'tb_permissions' => []
        ];

        $this->current_role = $role_name; 

        return $this;
    }

    public function addRoles(Array $roles) {
        foreach ($roles as $role_name => $role_id) {

            $this->addRole($role_name, $role_id);
        }

        $this->current_role = null;
        return $this;
    }    	
	    
    public function addInherit(string $role_name, $to_role = null) {
        if ($to_role != null){
            $this->current_role = $to_role;
        }

        if ($this->current_role == null){
            throw new \Exception("You can't inherit from undefined rol");
        }

        if (!isset($this->role_perms[$this->current_role]['sp_permissions'])){
            $this->role_perms[$this->current_role]['sp_permissions'] = [];
        } else {
            if (!empty($this->role_perms[$this->current_role]['sp_permissions'])){
                throw new \Exception("You can't inherit permissions from '$role_name' when you have already permissions for '".$this->current_role."'");
            }
        }

        if (!isset($this->role_perms[$this->current_role]['tb_permissions'])){
            $this->role_perms[$this->current_role]['tb_permissions'] = [];
        } else {
            if (!empty($this->role_perms[$this->current_role]['tb_permissions'])){
                throw new \Exception("You can't inherit permissions from '$role_name' when you have already permissions for '$this->current_role'");
            }
        }

        if (!empty($this->role_perms[$this->current_role]['sp_permissions']) || !empty($this->role_perms[$this->current_role]['sp_permissions'])){
            throw new \Exception("You can't inherit permissions from '$role_name' when you have already permissions for '$this->current_role'");
        }

        if (!isset($this->role_perms[$role_name]) || !isset($this->role_perms[$role_name]['sp_permissions']) || !isset($this->role_perms[$role_name]['tb_permissions']) ){
            throw new \Exception("[ Inherit ] Role '$role_name' not found");
        }

        $this->role_perms[$this->current_role]['sp_permissions'] = $this->role_perms[$role_name]['sp_permissions'];
        $this->role_perms[$this->current_role]['tb_permissions'] = $this->role_perms[$role_name]['tb_permissions'];

        return $this;
    }

    public function addSpecialPermissions(Array $sp_permissions, $to_role = null) {
        if ($to_role != null){
            $this->current_role = $to_role;
        }

        if ($this->current_role == null){
            throw new \Exception("You can't inherit from undefined rol");
        }

        // chequear que $sp_permissions no se cualquier cosa
        foreach ($sp_permissions as $spp){
            if (!in_array($spp, $this->sp_permissions)){
                throw new \Exception("'$spp' is not a valid special permission");
            }

            // caso especial de un pseudo-permiso
            if ($spp == 'grant'){
                $this->addResourcePermissions('tb_permissions', ['read', 'write']);
                //return $this;
            }
        }
        
        $this->role_perms[$this->current_role]['sp_permissions'] = array_unique(array_merge($this->role_perms[$this->current_role]['sp_permissions'], $sp_permissions));
     
        return $this;
    }
    
    public function addResourcePermissions(string $table, Array $tb_permissions, $to_role = null) {
        if ($to_role != null){
            $this->current_role = $to_role;
        }

        if ($this->current_role == null){
            throw new \Exception("You can't inherit from undefined rol");
        }

        if (!isset($this->role_perms[$this->current_role]['tb_permissions'][$table])){
            $this->role_perms[$this->current_role]['tb_permissions'][$table] = [];
        }

        foreach ($tb_permissions as $tbp){
            switch ($tbp) {
                case 'show':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'show';
                break;
                case 'show_all':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'show_all';
                break;
                case 'list':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'list';
                break;
                case 'list_all':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'list_all';
                break;
                case 'read':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'show';
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'list';
                break;
                case 'read_all':  // new
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'show_all';
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'list_all';
                break;

                case 'create':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'create';
                break;
                case 'update':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'update';
                break;
                case 'delete':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'delete';    
                break;
                case 'write':
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'create';
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'update';
                    $this->role_perms[$this->current_role]['tb_permissions'][$table][] = 'delete';
                break;

                default:
                    throw new \Exception("'$tbp' is not a valid resource permission");
            }
        }

        $this->role_perms[$this->current_role]['tb_permissions'][$table] = array_unique($this->role_perms[$this->current_role]['tb_permissions'][$table]);

        return $this;
    }



   public function setGuest(string $guest_name){
        if (!in_array($guest_name, $this->role_names)){
            throw new \Exception("Please add the rol '$guest_name' *before* to set as guest role to avoid mistakes");
        }

        $this->guest_name = $guest_name;
        return $this;
    }

    public function getGuest(){
        if ($this->guest_name == NULL){
            throw new \Exception("Undefined guest rol in ACL");
        }

        return $this->guest_name;
    }

    public function getRoleName($role_id = NULL){
        if ($role_id == NULL){
            return $this->role_names;
        }

        foreach ($this->role_perms as $name => $r){
            if ($r['role_id'] == $role_id){
                return $name;
            }
        }

        throw new \Exception("Undefined role for role_id '$role_id'");
    }

    public function getRoleId(string $role_name){
        if (isset($this->role_perms[$role_name])){
            return $this->role_perms[$role_name]['role_id'];
        }

        throw new \Exception("Undefined role with name '$role_name'");
    }

    public function roleExists(string $role_name){
        return isset($this->role_perms[$role_name]);
    }

    public function hasSpecialPermission(string $perm, Array $role_names){
        if (!in_array($perm, $this->sp_permissions)){
            throw new \InvalidArgumentException("hasSpecialPermission : invalid permission '$perm'");    
        }

        foreach ($role_names as $r_name){
            if (!isset($this->role_perms[$r_name])){
                throw new \InvalidArgumentException("hasSpecialPermission : invalid role name '$r_name'");
            }

            if (in_array($perm, $this->role_perms[$r_name]['sp_permissions'])){
                return true;
            } 
        }
        
        return false;
    }

    public function hasResourcePermission(string $perm, Array $role_names, string $resource){
        if (!in_array($perm, ['show', 'show_all', 'list', 'list_all', 'create', 'update', 'delete'])){
            throw new \InvalidArgumentException("hasResourcePermission : invalid permission '$perm'");    
        }

        foreach ($role_names as $r_name){
            if (!isset($this->role_perms[$r_name])){
                throw new \InvalidArgumentException("hasResourcePermission : invalid role name '$r_name'");
            }

            if (isset($this->role_perms[$r_name]['tb_permissions'][$resource])){
                if (in_array($perm, $this->role_perms[$r_name]['tb_permissions'][$resource])){
                    return true;
                }
            }  
        }
        
        return false;
    }

    public function getResourcePermissions(string $role, string $resource, $op_type = null){
        $ops = [
            'read'  => ['show', 'list', 'show_all', 'list_all'],
            'write' => ['create', 'update', 'delete']
        ];

        if ($op_type != null && ($op_type != 'read' && $op_type != 'write')){
            throw new \InvalidArgumentException("getResourcePermissions : '$op_type' is not a valid value for op_type");
        }
        
        if (isset($this->role_perms[$role]['tb_permissions'][$resource])){
            if ($op_type != null){
                return array_intersect($this->role_perms[$role]['tb_permissions'][$resource], $ops[$op_type]);
            }

            return $this->role_perms[$role]['tb_permissions'][$resource];
        }

        return [];
    }    

    public function getRolePermissions(){
        return $this->role_perms;
    }


    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    // Also needed but not in interface

    public function getTbPermissions(string $table = NULL){
        if (empty($this->permissions)){
            return NULL;
        }

        $tb_perms = $this->permissions['tb'];

        if ($table == NULL)
            return $tb_perms;

        if (!isset($tb_perms[$table]))
            return NULL;

        return $tb_perms[$table];
    }


    // Related with AuthController !

    function getUserIdFromApiKey($api_key){
        $uid = DB::table('api_keys')
        ->where(['value', $api_key])
        ->value('user_id');

        return $uid;
    }

    function fetchRoles($uid) : Array {
        $rows = DB::table('user_roles')
        ->assoc()
        ->where(['user_id', $uid])
        ->select(['role_id as role'])
        ->get();	

        $acl = Factory::acl();

        $roles = [];
        if (count($rows) != 0){
            foreach ($rows as $row){
                $roles[] = $acl->getRoleName($row['role']);
            }
        }

        //dd($roles, 'roles');
        return $roles;
    }

    function fetchTbPermissions($uid) : Array {
        $_permissions = DB::table('user_tb_permissions')
        ->assoc()
        ->select([  
                    'tb', 
                    'can_list_all as la',
                    'can_show_all as ra', 
                    'can_list as l',
                    'can_show as r',
                    'can_create as c',
                    'can_update as u',
                    'can_delete as d'])
        ->where(['user_id' => $uid])
        ->get();

        $perms = [];
        foreach ((array) $_permissions as $p){
            $tb = $p['tb'];
            $perms[$tb] =  $p['la'] * 64 + $p['ra'] * 32 +  $p['l'] * 16 + $p['r'] * 8 + $p['c'] * 4 + $p['u'] * 2 + $p['d'];
        }

        return $perms;
    }

    function fetchSpPermissions($uid) : Array {
        $perms = DB::table('user_sp_permissions')
        ->assoc()
        ->where(['user_id' => $uid])
        ->join('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
        ->pluck('name');

        return $perms ?? [];
    }

    function fetchPermissions($uid) : Array { 
        return [
                'tb' => $this->fetchTbPermissions($uid), 
                'sp' => $this->fetchSpPermissions($uid) 
        ];
    }

    public function addUserRoles(Array $roles, $uid) {
        foreach ($roles as $role) {
            $role_id = $this->getRoleId($role);

            if ($role_id == null){
                throw new \Exception("Role $role is invalid");
            }
            
            // lo ideal es validar los roles y obtener los ids para luego hacer un "INSERT in bulk"
            $ur_id = DB::table('user_roles')
            ->where(['id' => $uid])
            ->create(['user_id' => $uid, 'role_id' => $role_id]);

            if (empty($ur_id))
                throw new \Exception("Error registrating user role $role");             
        }         
    }

    public function isGuest(){
        return $this->roles == [$this->getGuest()];
    }

    public function isRegistered(){
        return !$this->isGuest();
    }

    public function getRoles(){
        return $this->roles;
    }

}

