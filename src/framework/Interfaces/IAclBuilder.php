<?php

namespace Boctulus\Simplerest\Core\Interfaces;

/**
 * Build-time contract: define roles, inheritance, and permissions.
 * Zero dependency on auth(), request(), or DB.
 * Used exclusively during ACL policy construction (config/acl.php or equivalent).
 */
interface IAclBuilder
{
    public function addRole(string $role_name, $role_id = null);

    public function addRoles(array $roles);

    public function addInherit(string $role_name, $to_role = null);

    public function addSpecialPermissions(array $sp_permissions, $to_role = null);

    public function addResourcePermissions(string $table, array $tb_permissions, $to_role = null);

    public function setAsGuest(string $guest_name);

    public function setAsRegistered(string $name);
}
