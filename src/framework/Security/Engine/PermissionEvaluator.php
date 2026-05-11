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
     * Bit positions for packed user_tb_permissions
     * (mirrors AuthController::fetchTbPermissions packing order).
     */
    private const TB_BIT_LIST_ALL = 64;
    private const TB_BIT_SHOW_ALL = 32;
    private const TB_BIT_LIST     = 16;
    private const TB_BIT_SHOW     = 8;
    private const TB_BIT_CREATE   = 4;
    private const TB_BIT_UPDATE   = 2;
    private const TB_BIT_DELETE   = 1;

    /**
     * Maps a permission name to its bit flag in the packed int.
     */
    private function tbBit(string $perm): int
    {
        return match ($perm) {
            'list_all' => self::TB_BIT_LIST_ALL,
            'show_all' => self::TB_BIT_SHOW_ALL,
            'list'     => self::TB_BIT_LIST,
            'show'     => self::TB_BIT_SHOW,
            'create'   => self::TB_BIT_CREATE,
            'update'   => self::TB_BIT_UPDATE,
            'delete'   => self::TB_BIT_DELETE,
            default    => 0,
        };
    }

    /**
     * Full permission evaluation:
     *   read-type perms  → short-circuit on read_all  special permission
     *   write-type perms → short-circuit on write_all special permission
     *   user-level tb override → replaces role-level if present (replacement semantics)
     *   then falls back to role-level resource check
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

        // User-level tb override (replacement semantics from user_tb_permissions)
        $userPacked = $context->userTbPerms[$resource] ?? null;
        if ($userPacked !== null) {
            $bit = $this->tbBit($perm);
            return $bit !== 0 && ($userPacked & $bit) !== 0;
        }

        return $this->hasResourcePermission($perm, $resource, $context->roles, $snapshot);
    }
}
