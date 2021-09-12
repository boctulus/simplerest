<?php

namespace boctulus\basic_acl;

use simplerest\libs\DB;
use simplerest\libs\Factory;

class Acl extends \simplerest\core\Acl
{
    protected $roles = [];
    protected $role_perms = [];
    protected $role_ids   = [];
    protected $role_names = [];
    protected $sp_permissions = []; 
    protected $current_role;
    protected $guest_name = 'guest';


    public function __construct() { 
        $this->config = Factory::config();
        $this->setup();
    }

 
    protected function setup(){        
        $this->roles = DB::table('roles')->get();

        foreach($this->roles as $rr){
            $this->role_names[] = $rr['name'];
            $this->role_ids[]   = $rr['id'];
        }
    }
    
    // Not in interfaces neither needed 

    public function hasRole(string $role){
        return in_array($role, $this->roles);
    }

    public function hasAnyRole(array $authorized_roles){
        $authorized = false;
        foreach ((array) $this->roles as $role)
            if (in_array($role, $authorized_roles))
                $authorized = true;

        return $authorized;        
    }

    public function getSpPermissions(){
        return $this->sp_permissions;
    }


}

