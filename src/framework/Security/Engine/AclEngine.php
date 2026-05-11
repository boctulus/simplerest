<?php

namespace Boctulus\Simplerest\Core\Security\Engine;

use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationServiceInterface;
use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationPolicyInterface;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

final class AclEngine implements AuthorizationServiceInterface
{
    public function __construct(
        private readonly AclSnapshot $snapshot,
    ) {}

    // ── Per-action evaluation ──────────────────────────────────────────────

    public function can(AclContext $context, string $action, string $resource): bool
    {
        return $this->hasPermissionInternal($action, $resource, $context);
    }

    public function hasSpecialPermission(string $perm, AclContext $context): bool
    {
        return $this->hasSpecialPermissionInternal($perm, $context);
    }

    public function hasResourcePermission(string $perm, string $resource, AclContext $context): bool
    {
        return $this->hasResourcePermissionInternal($perm, $resource, $context->roles);
    }

    // ── Role membership (no hierarchy) ────────────────────────────────────

    public function hasRole(string $role, AclContext $context): bool
    {
        return in_array($role, $context->roles, true);
    }

    public function hasAnyRole(array $roles, AclContext $context): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role, $context)) {
                return true;
            }
        }

        return false;
    }

    // ── Capability / permission-set evaluation ────────────────────────────

    public function hasAllPermissions(AclContext $context, array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (!$this->resolvePermission($context, $perm)) {
                return false;
            }
        }

        return true;
    }

    public function hasAnyPermission(AclContext $context, array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if ($this->resolvePermission($context, $perm)) {
                return true;
            }
        }

        return false;
    }

    public function roleDominates(string $candidateRole, string $targetRole): bool
    {
        $target = $this->effectiveRolePermissions($targetRole);

        if (empty($target)) {
            return true;
        }

        $candidate = $this->effectiveRolePermissions($candidateRole);

        foreach ($target as $perm) {
            if (!in_array($perm, $candidate, true)) {
                return false;
            }
        }

        return true;
    }

    public function satisfiesPolicy(AclContext $context, AuthorizationPolicyInterface $policy): bool
    {
        return $policy->isSatisfiedBy($context, $this);
    }

    // ── Role hierarchy methods (lineage-based) ─────────────────────────────

    public function getAncestry(string $role): array
    {
        return $this->getAncestryInternal($role, $this->snapshot->parentRoleNames);
    }

    public function isHigherRole(string $role, string $referenced): ?bool
    {
        if ($role === $referenced) {
            return false;
        }

        $parentRoleNames = $this->snapshot->parentRoleNames;

        if (in_array($role, $this->getAncestryInternal($referenced, $parentRoleNames), true)) {
            return false;
        }

        if (in_array($referenced, $this->getAncestryInternal($role, $parentRoleNames), true)) {
            return true;
        }

        return null;
    }

    public function hasRoleOrHigher(string $role, AclContext $context): bool
    {
        foreach ($context->roles as $userRole) {
            if ($userRole === $role || $this->isHigherRole($userRole, $role) === true) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRoleOrHigher(array $roles, AclContext $context): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRoleOrHigher($role, $context)) {
                return true;
            }
        }

        return false;
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getSnapshot(): AclSnapshot
    {
        return $this->snapshot;
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function resolvePermission(AclContext $context, string $permission): bool
    {
        if (str_contains($permission, '.')) {
            [$resource, $action] = explode('.', $permission, 2);

            return $this->can($context, $action, $resource);
        }

        return $this->hasSpecialPermissionInternal($permission, $context);
    }

    private function effectiveRolePermissions(string $role): array
    {
        if (!isset($this->snapshot->rolePerms[$role])) {
            return [];
        }

        $perms = $this->snapshot->rolePerms[$role]['sp_permissions'];

        foreach ($this->snapshot->rolePerms[$role]['tb_permissions'] as $resource => $actions) {
            foreach ($actions as $action) {
                $perms[] = "$resource.$action";
            }
        }

        return $perms;
    }

    // ── Permission evaluation internals (formerly in PermissionEvaluator) ─

    private function hasSpecialPermissionInternal(string $perm, AclContext $context): bool
    {
        if (in_array($perm, $context->userSpPerms, true)) {
            return true;
        }

        foreach ($context->roles as $role) {
            if (!isset($this->snapshot->rolePerms[$role])) {
                continue;
            }

            if (in_array($perm, $this->snapshot->rolePerms[$role]['sp_permissions'], true)) {
                return true;
            }
        }

        return false;
    }

    private function hasResourcePermissionInternal(string $perm, string $resource, array $roles): bool
    {
        foreach ($roles as $role) {
            if (!isset($this->snapshot->rolePerms[$role])) {
                continue;
            }

            $tbPerms = $this->snapshot->rolePerms[$role]['tb_permissions'][$resource] ?? [];

            if (in_array($perm, $tbPerms, true)) {
                return true;
            }
        }

        return false;
    }

    private const TB_BIT_LIST_ALL = 64;
    private const TB_BIT_SHOW_ALL = 32;
    private const TB_BIT_LIST     = 16;
    private const TB_BIT_SHOW     = 8;
    private const TB_BIT_CREATE   = 4;
    private const TB_BIT_UPDATE   = 2;
    private const TB_BIT_DELETE   = 1;

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

    private function hasPermissionInternal(
        string     $perm,
        string     $resource,
        AclContext $context
    ): bool {
        static $readPerms  = ['show', 'list', 'read', 'show_all', 'list_all', 'read_all'];
        static $writePerms = ['create', 'update', 'delete', 'write', 'write_all'];

        if (in_array($perm, $readPerms, true)) {
            if ($this->hasSpecialPermissionInternal('read_all', $context)) {
                return true;
            }
        }

        if (in_array($perm, $writePerms, true)) {
            if ($this->hasSpecialPermissionInternal('write_all', $context)) {
                return true;
            }
        }

        $userPacked = $context->userTbPerms[$resource] ?? null;
        if ($userPacked !== null) {
            $bit = $this->tbBit($perm);
            return $bit !== 0 && ($userPacked & $bit) !== 0;
        }

        return $this->hasResourcePermissionInternal($perm, $resource, $context->roles);
    }

    // ── Hierarchy internals (formerly in RoleHierarchyResolver) ───────────

    private function getAncestryInternal(string $role, array $parentRoleNames): array
    {
        $ancestors = [];
        $current   = $role;

        while (isset($parentRoleNames[$current])) {
            $current     = $parentRoleNames[$current];
            $ancestors[] = $current;
        }

        return $ancestors;
    }
}
