<?php

namespace Boctulus\Simplerest\Core\Security\Engine;

use Boctulus\Simplerest\Core\Security\Contracts\PermissionEvaluatorInterface;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

final class PermissionEvaluator implements PermissionEvaluatorInterface
{
    /**
     * Checks the special-permission against:
     *   1. User-level sp overrides carried in AclContext::$userSpPerms
     *   2. Role-level sp_permissions baked into the snapshot
     *
     * NOTE: role_perms already contains inherited permissions (merged at build time
     * via addInherit()), so no ancestry expansion is needed here.
     */
    public function hasSpecialPermission(
        string     $perm,
        AclContext $context,
        AclSnapshot $snapshot
    ): bool {
        if (in_array($perm, $context->userSpPerms, true)) {
            return true;
        }

        foreach ($context->roles as $role) {
            if (!isset($snapshot->rolePerms[$role])) {
                continue;
            }

            if (in_array($perm, $snapshot->rolePerms[$role]['sp_permissions'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks tb_permissions for the given resource.
     * Uses $roles parameter (not context) so callers can pass any role set.
     */
    public function hasResourcePermission(
        string      $perm,
        string      $resource,
        array       $roles,
        AclSnapshot $snapshot
    ): bool {
        foreach ($roles as $role) {
            if (!isset($snapshot->rolePerms[$role])) {
                continue;
            }

            $tbPerms = $snapshot->rolePerms[$role]['tb_permissions'][$resource] ?? [];

            if (in_array($perm, $tbPerms, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Full permission evaluation:
     *   read-type perms  → short-circuit on read_all  special permission
     *   write-type perms → short-circuit on write_all special permission
     *   then falls back to resource-level check
     */
    public function hasPermission(
        string      $perm,
        string      $resource,
        AclContext  $context,
        AclSnapshot $snapshot
    ): bool {
        static $readPerms  = ['show', 'list', 'read', 'show_all', 'list_all', 'read_all'];
        static $writePerms = ['create', 'update', 'delete', 'write', 'write_all'];

        if (in_array($perm, $readPerms, true)) {
            if ($this->hasSpecialPermission('read_all', $context, $snapshot)) {
                return true;
            }
        }

        if (in_array($perm, $writePerms, true)) {
            if ($this->hasSpecialPermission('write_all', $context, $snapshot)) {
                return true;
            }
        }

        return $this->hasResourcePermission($perm, $resource, $context->roles, $snapshot);
    }
}
