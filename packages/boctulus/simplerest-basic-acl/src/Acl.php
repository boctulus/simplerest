<?php

namespace Boctulus\BasicACL;

use Boctulus\Simplerest\Core\Libs\DB;

class Acl extends \Boctulus\Simplerest\Core\Security\Acl
{
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

    /**
     * Registers every row of the `roles` table as a role, with empty
     * permissions. config/acl.php then assigns permissions with the
     * $to_role argument of addSpecialPermissions()/addResourcePermissions().
     */
    protected function setup(){
        $this->roles = DB::table('roles')->get();

        foreach($this->roles as $rr){
            // Same state as Core\Security\Acl::addRole(), including role_perms.
            $this->registerRoleState($rr['name'], $rr['id']);
        }

        $this->current_role = null;
    }

}

