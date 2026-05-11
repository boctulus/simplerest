<?php

namespace Boctulus\Simplerest\Core\Security\Engine;

use Boctulus\Simplerest\Core\Security\Contracts\AclEngineInterface;
use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationPolicyInterface;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

/**
 * Pure ACL evaluation engine.
 *
 * Zero dependencies on DB, auth(), request(), or any framework global.
 * All inputs are injected: AclSnapshot (policy) + AclContext (runtime user state).
 *
 * Hierarchy methods (isHigherRole, hasRoleOrHigher, getAncestry) have been moved
 * to RoleHierarchyService — they compare role lineage, not effective permissions,
 * and therefore do not belong in an authorization engine.
 *
 * Instantiate via Acl::getEngine() for framework use,
 * or directly with a hand-crafted AclSnapshot for unit tests.
 */
final class AclEngine implements AclEngineInterface
{
    private readonly PermissionEvaluator $evaluator;

    public function __construct(
        private readonly AclSnapshot  $snapshot,
        ?PermissionEvaluator          $evaluator = null,
    ) {
        $this->evaluator = $evaluator ?? new PermissionEvaluator();
    }

    // ── Per-action evaluation ──────────────────────────────────────────────

    public function can(AclContext $context, string $action, string $resource): bool
    {
        return $this->evaluator->hasPermission($action, $resource, $context, $this->snapshot);
    }

    public function hasSpecialPermission(string $perm, AclContext $context): bool
    {
        return $this->evaluator->hasSpecialPermission($perm, $context, $this->snapshot);
    }

    public function hasResourcePermission(string $perm, string $resource, AclContext $context): bool
    {
        return $this->evaluator->hasResourcePermission($perm, $resource, $context->roles, $this->snapshot);
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

    /**
     * True if the user satisfies ALL of the given permissions.
     *
     * Permission format:
     *   "resource.action"  → e.g. "products.create", "users.list_all"
     *   "sp_perm_name"     → e.g. "read_all", "impersonate"
     */
    public function hasAllPermissions(AclContext $context, array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (!$this->resolvePermission($context, $perm)) {
                return false;
            }
        }

        return true;
    }

    /**
     * True if the user satisfies AT LEAST ONE of the given permissions.
     */
    public function hasAnyPermission(AclContext $context, array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if ($this->resolvePermission($context, $perm)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set-theory dominance: effective_permissions(candidate) ⊇ effective_permissions(target).
     *
     * Does NOT traverse role lineage. Compares the explicit permission sets baked
     * into the snapshot at build time (after inheritance was resolved via addInherit).
     * A role with zero permissions is dominated by any role (vacuous truth).
     */
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

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getSnapshot(): AclSnapshot
    {
        return $this->snapshot;
    }

    public function getPermissionEvaluator(): PermissionEvaluator
    {
        return $this->evaluator;
    }

    // ── Private helpers ───────────────────────────────────────────────────

    /**
     * Resolves a permission string to a boolean for the given context.
     * Format: "resource.action" or bare special-permission name.
     */
    private function resolvePermission(AclContext $context, string $permission): bool
    {
        if (str_contains($permission, '.')) {
            [$resource, $action] = explode('.', $permission, 2);
            return $this->can($context, $action, $resource);
        }

        return $this->evaluator->hasSpecialPermission($permission, $context, $this->snapshot);
    }

    /**
     * Returns the flat effective permission set for a role as stored in the snapshot.
     * Format: ["sp_perm", "resource.action", ...]
     * Note: snapshot already has inherited permissions merged via addInherit().
     */
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
}
