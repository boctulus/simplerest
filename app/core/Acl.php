<?php

namespace simplerest\core;

use simplerest\core\interfaces\IAcl;
use simplerest\core\libs\Files;

abstract class Acl implements IAcl
{
    protected $roles = [];

    protected $role_ids   = [];
    protected $role_names = [];
    protected $role_perms = [];
    protected $parent_role_names = [];
    protected $current_role;
    protected $guest_name = 'guest';
    protected $registered_name = 'registered';
    protected $sp_permissions = [];
    protected $ancestors = []; 
    

    public function __construct()
    {
        $this->sp_permissions = [
            'read_all',
            'read_all_folders',
            'read_all_trashcan',
            'write_all',            
            'write_all_folders',            
            'write_all_trashcan',
            'write_all_collections',
            'fill_all',
            'grant',
            'impersonate',
            'lock',
            'transfer'
        ]; 

        if (!is_dir(SECURITY_PATH)){
            Files::mkDirOrFail(SECURITY_PATH);
        }
    
        Files::writableOrFail(SECURITY_PATH);
        Files::writableOrFail(SECURITY_PATH . config()['acl_file']);
    }

    public function getEveryPossibleRole(){
        return $this->roles;
    }

    public function addRole(string $role_name, $role_id = null) 
    {  
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
            'role_id'        => $role_id,
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

        $this->parent_role_names[$this->current_role] = $role_name;

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
                case 'read_all':  
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

   public function setAsGuest(string $guest_name){
        if (!in_array($guest_name, $this->role_names)){
            throw new \Exception("Please add the rol '$guest_name' *before* to set as guest role to avoid mistakes");
        }

        $this->guest_name = $guest_name;
        return $this;
    }

    function setAsRegistered(string $name){
        if (!in_array($name, $this->role_names)){
            throw new \Exception("Please add the rol '$name' *before* to set as registered role to avoid mistakes");
        }

        $this->registered_name = $name;
        return $this;
    }

    public function getGuest(){
        if ($this->guest_name == null){
            throw new \Exception("Undefined guest rol in ACL");
        }

        return $this->guest_name;
    }

    public function getRegistered(){
        if ($this->registered_name == null){
            throw new \Exception("Undefined guest rol in ACL");
        }

        return $this->registered_name;
    }

    public function getRoleName($role_id = null){
        if ($role_id === null){
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

    public function hasSpecialPermission(string $perm, ?Array $role_names = null, $uid = null){
        if (empty($role_names)){
            $role_names = auth()->getRoles();
        }

        // Podria chequearse si un usuario dado su uid tiene el permiso
        if ($uid !== null){
            // ...
        }

        if (!in_array($perm, $this->sp_permissions)){
            throw new \InvalidArgumentException("Invalid permission '$perm'");    
        }

        foreach ($role_names as $r_name){
            if (!isset($this->role_perms[$r_name])){
                throw new \InvalidArgumentException("Invalid role name '$r_name'");
            }

            if (in_array($perm, $this->role_perms[$r_name]['sp_permissions'])){
                return true;
            } 
        }
        
        return false;
    }

    public function hasResourcePermission(string $perm, string $resource, ?Array $role_names = null){
        if (empty($role_names)){
            $role_names = $role_names = auth()->getRoles();
        }

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

    public function getRolePermissions(string $role_name = null){
        if (!is_null($role_name)){
            return $this->role_perms[$role_name];
        } else {
            return $this->role_perms;
        }        
    }

    public function getAncestry(string $role){
        if (isset($this->ancestors[$role])){
            return $this->ancestors[$role];
        }

        $ref = $role;

        while (true){
            $role = $this->parent_role_names[$role] ?? null;

            if ($role === null){
                break;
            }

            $ancestors[$ref][] = $role;
        }

        return $ancestors[$ref];
    }

    /*
        Every possible Special Permission for the ACL 
    */
    public function getEveryPossibleSpPermissions(){
        return $this->sp_permissions;
    }

    protected function unpackTbPermissions($permissions){
        if (empty($permissions)){
            return [];
        }

        if (is_int($permissions)){
            $perms = $permissions;
            return [
                'list_all' => ($perms & 64) AND 1,
                'show_all' => ($perms & 32 ) AND 1,
                'list'     => ($perms & 16) AND 1, 
                'show'     => ($perms & 8 ) AND 1, 
                'create'   => ($perms & 4 ) AND 1, 
                'update'   => ($perms & 2 ) AND 1, 
                'delete'   => ($perms & 1 ) AND 1
            ];
        }

        $tb_perm_unpacked = [];
        foreach ($permissions as $tb => $perms){
            $perms = (int) $perms;

            $tb_perm_unpacked[$tb] = [
                'list_all' => ($perms & 64) AND 1,
                'show_all' => ($perms & 32 ) AND 1,
                'list'     => ($perms & 16) AND 1, 
                'show'     => ($perms & 8 ) AND 1, 
                'create'   => ($perms & 4 ) AND 1, 
                'update'   => ($perms & 2 ) AND 1, 
                'delete'   => ($perms & 1 ) AND 1
            ];
        }

        return $tb_perm_unpacked;
    }

    /*
        Permissions can not be "fresh" if it comes from an Web Token
    */
    public function getTbPermissions(string $table = null, bool $unpacked = true){
        $current_user_permissions = auth()->getCurrentPermissions();

        if (empty($current_user_permissions)){
            return null;
        }

        $tb_perms = $current_user_permissions['tb'];

        if ($table == null)
            return $unpacked ? $this->unpackTbPermissions($tb_perms) : $tb_perms;

        if (!isset($tb_perms[$table]))
            return null;

        return $unpacked ? $this->unpackTbPermissions($tb_perms[$table]) : $tb_perms[$table];
    }

    /*
        Permissions can not be "fresh" if it comes from an Web Token
    */
    public function getSpPermissions(string $table = null){
        $current_user_permissions = auth()->getCurrentPermissions();

        if (empty($current_user_permissions)){
            return null;
        }

        $tb_perms = $current_user_permissions['sp'];

        if ($table == null)
            return $tb_perms;

        if (!isset($tb_perms[$table]))
            return null;

        return $tb_perms[$table];
    }


    // fiexed
    public function isGuest() : bool {
        return auth()->getRoles() == [$this->getGuest()];
    }

    public function isRegistered() : bool {
        return !$this->isGuest();
    }

    // Not in interfaces 

    public function hasRole(string $role) : bool {
        return in_array($role, auth()->getRoles());
    }

    /*
        Return if $role is higher role than $referenced_role

        Warning: this function compares lineages and not permissions  !!!
    */
    public function isHigherRole(string $role, string $referenced_role){
        if ($role == $referenced_role){
            return false;
        }

        // ancesters has inferior role permissions
        $ancestors_ref = $this->getAncestry($referenced_role);

        /*
            Si el rol está dentro de los ancestros del de referencia significa que es inferior
        */
        if (in_array($role, $ancestors_ref)){
            return false;
        }

        $ancestors = $this->getAncestry($role);

        if (in_array($referenced_role, $ancestors)){
            return true;
        }

        return null;
    }

    public function hasRoleOrHigher(string $role){
        if ($this->hasRole($role)){
            return true;
        }

        $current_user_roles = auth()->getRoles();

        foreach ($current_user_roles as $user_role){
            if ($this->isHigherRole($user_role, $role)){
                return true;
            }
        }

        return false;        
    }

    // alias
    public function hasRoleOrChild(string $role){
        return $this->hasRoleOrHigher($role);
    }

    public function hasAnyRole(array $authorized_roles){
        $authorized = false;
        
        foreach ($authorized_roles as $role){
            if ($this->hasRole($role)){
                $authorized = true;
            }
        }
        
        return $authorized;        
    }

    public function hasAnyRoleOrHigher(array $authorized_roles){
        $authorized = false;
        
        foreach ($authorized_roles as $role){
            if ($this->hasRoleOrHigher($role)){
                $authorized = true;
            }
        }
        
        return $authorized;        
    }

    // alias
    function hasAnyRoleOrChild(array $authorized_roles){
        return $this->hasAnyRoleOrHigher($authorized_roles);
    }
}

