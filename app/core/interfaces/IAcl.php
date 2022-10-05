<?php

namespace simplerest\core\interfaces;

interface IAcl {
    function addRole(string $role_name, $role_id = null);

    function addRoles(Array $roles);

    function addInherit(string $role_name, $to_role = null);

    function addSpecialPermissions(Array $sp_permissions, $to_role = null);
    
    function addResourcePermissions(string $table, Array $tb_permissions, $to_role = null);
    
    function setAsGuest(string $guest_name);

    function getGuest();

    function getRoleName($role_id = null);

    function getRoleId(string $role_name);

    function roleExists(string $role_name);

    function hasSpecialPermission(string $perm, ?Array $role_names = null, $id = null);

    function hasResourcePermission(string $perm, string $resource, ?Array $role_names = []);
    
    //function isAllowed(string $op_type, Array $role_names, $resource);

    function getResourcePermissions(string $role, string $resource, $op_type = null);

    function getRolePermissions(); 

    function getAncestry(string $role);

    function getEveryPossibleSpPermissions();

    function getTbPermissions(string $table = null, bool $unpacked = true);

    function getSpPermissions(string $table = null);

    function isGuest() : bool;

    function isRegistered() : bool;

    function hasRole(string $role) : bool;

    function isHigherRole(string $role, string $referenced_role);

    function hasRoleOrHigher(string $role);

    function hasRoleOrChild(string $role);

    function hasAnyRole(array $authorized_roles);

    function hasAnyRoleOrChild(array $authorized_roles);

}