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
        $this->setup();
    }

 
    protected function setup(){        
        $this->roles = DB::table('roles')->get();

        foreach($this->roles as $rr){
            $this->role_names[] = $rr['name'];
            $this->role_ids[]   = $rr['id'];
        }
    }

}

