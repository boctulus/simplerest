<?php

namespace Boctulus\Simplerest\Core\Security\Engine;

use Boctulus\Simplerest\Core\Security\Contracts\AclEngineInterface;
use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationServiceInterface;
use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationPolicyInterface;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Service\RoleHierarchyService;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

final class AclEngine implements AuthorizationServiceInterface, AclEngineInterface
{
    private const TB_BIT_LIST_ALL = 64;
    private const TB_BIT_SHOW_ALL = 32;
    private const TB_BIT_LIST     = 16;
    private const TB_BIT_SHOW     = 8;
    private const TB_BIT_CREATE   = 4;
    private const TB_BIT_UPDATE   = 2;
    private const TB_BIT_DELETE   = 1;

    private RoleHierarchyService $hierarchyService;

    public function __construct(
        private readonly AclSnapshot $snapshot,
    ) {
        $this->hierarchyService = new RoleHierarchyService($snapshot);
    }

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
        if ($context->compiledPermissions !== null) {
            $cp = $context->compiledPermissions;
            if (isset($cp['deny'][$resource][$perm]))   return false;
            if (isset($cp['deny']['*'][$perm]))         return false;
            return isset($cp['allow'][$resource][$perm]);
        }

        if ($this->hasExplicitDeny($context, $perm, $resource)) {
            return false;
        }

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

    public function hasExplicitDeny(AclContext $context, string $action, string $resource): bool
    {
        if (isset($context->userDenyPerms[$resource][$action])) {
            return true;
        }

        foreach ($context->roles as $role) {
            if (isset($this->snapshot->denyRolePerms[$role]['tb'][$resource][$action])) {
                return true;
            }
        }

        return false;
    }

    private function hasExplicitSpecialDeny(string $perm, AclContext $context): bool
    {
        if (isset($context->userDenySpPerms[$perm])) {
            return true;
        }

        foreach ($context->roles as $role) {
            if (isset($this->snapshot->denyRolePerms[$role]['sp'][$perm])) {
                return true;
            }
        }

        return false;
    }

    // ── Role hierarchy methods (lineage-based) ─────────────────────────────

    public function getAncestry(string $role): array
    {
        return $this->hierarchyService->getAncestry($role);
    }

    public function isHigherRole(string $role, string $referenced): ?bool
    {
        return $this->hierarchyService->isHigherRole($role, $referenced);
    }

    public function hasRoleOrHigher(string $role, AclContext $context): bool
    {
        return $this->hierarchyService->hasRoleOrHigher($role, $context);
    }

    public function hasAnyRoleOrHigher(array $roles, AclContext $context): bool
    {
        return $this->hierarchyService->hasAnyRoleOrHigher($roles, $context);
    }

    public function hasRolePermissionsOrHigher(AclContext $context, string $targetRole): bool
    {
        return $this->hierarchyService->hasRolePermissionsOrHigher($context, $targetRole, $this);
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
        // Compiled fast path — O(1) hash lookups
        if ($context->compiledPermissions !== null) {
            $cp = $context->compiledPermissions;
            if (isset($cp['deny']['__sp__'][$perm])) {
                return false;
            }
            return isset($cp['allow']['__sp__'][$perm]);
        }

        // Legacy path — explicit deny short-circuits ALLOW
        if ($this->hasExplicitSpecialDeny($perm, $context)) {
            return false;
        }

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

    private function hasPermissionInternal(
        string     $perm,
        string     $resource,
        AclContext $context
    ): bool {
        // Compiled fast path — O(1) hash lookups, deny first
        if ($context->compiledPermissions !== null) {
            $cp = $context->compiledPermissions;
            if (isset($cp['deny'][$resource][$perm]))   return false;
            if (isset($cp['deny']['*'][$perm]))         return false;
            if (isset($cp['allow']['*'][$perm]))        return true;  // read_all/write_all sentinel
            return isset($cp['allow'][$resource][$perm]);
        }

        // Legacy path — explicit deny short-circuits ALLOW
        if ($this->hasExplicitDeny($context, $perm, $resource)) {
            return false;
        }

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


}
