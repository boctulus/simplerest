<?php

namespace boctulus\grained_acl;

use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class Acl extends \simplerest\core\Acl
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
        $this->setup();
    }

 
    protected function setup(){        
        // get all available sp_permissions
        $this->sp_permissions = DB::table('sp_permissions')->pluck('name');

        // get all available roles
        $this->roles = DB::table('roles')->get();

        foreach($this->roles as $rr){
            $this->role_names[] = $rr['name'];
            $this->role_ids[]   = $rr['id'];
        }
    }

    public function addRole(string $role_name, $role_id = NULL) {
        $create = true;

        if (in_array($role_id, $this->role_ids)){
            $create = false;

            foreach ($this->roles as $rr){
                if ($rr['id'] == $role_id && $rr['name'] != $role_name){
                    throw new \Exception("Role id '$role_id' can not be repetead. Trying to assign to '$role_name' but it was used for '{$rr['name']}' and it should be UNIQUE.");      
                }
            }
        }

        if (in_array($role_name, $this->role_names)){
            $create = false;
            
            foreach ($this->roles as $rr){
                if ($rr['id'] != $role_id && $rr['name'] == $role_name){
                    if ($role_id != NULL) {
                        throw new \Exception("Role name '$role_name' can not be repetead. Trying to assign to id '$role_id' but it was used for '{$rr['id']}' and it should be UNIQUE.");  
                    }
                       
                }
            }
        }

        if ($role_name == 'guest'){
            $this->guest_name = 'guest';
        }

        if ($create){
            $role_id = DB::table('roles')->create([
                'id'   => $role_id,
                'name' => $role_name
            ]);
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

}

