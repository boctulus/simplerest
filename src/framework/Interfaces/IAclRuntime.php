<?php

namespace Boctulus\Simplerest\Core\Interfaces;

/**
 * Runtime evaluation contract: answer permission questions for the current user.
 * Auth-aware: implementations may call auth(), request(), or DB internally.
 * This is the interface controllers and middleware should type-hint against.
 */
interface IAclRuntime
{
    public function hasRole(string $role): bool;

    public function hasAnyRole(array $authorized_roles): bool;

    public function hasRoleOrHigher(string $role): bool;

    public function hasRoleOrChild(string $role): bool;

    public function hasAnyRoleOrHigher(array $authorized_roles): bool;

    public function hasAnyRoleOrChild(array $authorized_roles): bool;

    public function hasSpecialPermission(string $perm, ?array $role_names = null, $uid = null): bool;

    public function hasResourcePermission(string $perm, string $resource, ?array $role_names = null): bool;

    public function hasPermission(string $perm, string $resource, $uid = null, $row_id = null): bool;

    public function getTbPermissions(string $table = null, bool $unpacked = true);

    public function getSpPermissions(string $table = null);
}
