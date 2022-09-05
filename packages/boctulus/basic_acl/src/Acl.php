<?php

namespace boctulus\basic_acl;

use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

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
        // get all available roles  
        $this->roles = DB::table('roles')->get();

        // podría reemplazarse por dos array_column()
        foreach($this->roles as $rr){
            $this->role_names[] = $rr['name'];
            $this->role_ids[]   = $rr['id'];
        }
    }

}

