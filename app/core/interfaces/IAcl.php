<?php

namespace simplerest\core\interfaces;

interface IAcl {
    public function addRole(string $role_name, $role_id = null);

    public function addRoles(Array $roles);

    public function addInherit(string $role_name, $to_role = null);

    public function addSpecialPermissions(Array $sp_permissions, $to_role = null);
    
    public function addResourcePermissions(string $table, Array $tb_permissions, $to_role = null);
    
    public function setGuest(string $guest_name);

    public function getGuest();

    public function getRoleName($role_id = null);

    public function getRoleId(string $role_name);

    public function roleExists(string $role_name);

    public function hasSpecialPermission(string $perm, Array $role_names);

    public function hasResourcePermission(string $perm, Array $role_names, string $resource);
    
    //public function isAllowed(string $op_type, Array $role_names, $resource);

    public function getResourcePermissions(string $role, string $resource, $op_type = null);

    public function getRolePermissions(); 

}