<?php

namespace Boctulus\Simplerest\Core\Security\Engine;

use Boctulus\Simplerest\Core\Security\Contracts\AclEngineInterface;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

/**
 * Pure ACL evaluation engine.
 *
 * Zero dependencies on DB, auth(), request(), or any framework global.
 * All inputs are injected: AclSnapshot (policy) + AclContext (runtime user state).
 *
 * Instantiate via Acl::getEngine() for framework use,
 * or directly with a hand-crafted AclSnapshot for unit tests.
 */
final class AclEngine implements AclEngineInterface
{
    private readonly RoleHierarchyResolver $hierarchy;
    private readonly PermissionEvaluator   $evaluator;

    public function __construct(
        private readonly AclSnapshot $snapshot,
        ?RoleHierarchyResolver       $hierarchy = null,
        ?PermissionEvaluator         $evaluator = null,
    ) {
        $this->hierarchy = $hierarchy ?? new RoleHierarchyResolver();
        $this->evaluator = $evaluator ?? new PermissionEvaluator();
    }

    // -------------------------------------------------------------------------
    // Permission checks
    // -------------------------------------------------------------------------

    public function can(AclContext $context, string $perm, string $resource): bool
    {
        return $this->evaluator->hasPermission($perm, $resource, $context, $this->snapshot);
    }

    public function hasSpecialPermission(string $perm, AclContext $context): bool
    {
        return $this->evaluator->hasSpecialPermission($perm, $context, $this->snapshot);
    }

    public function hasResourcePermission(string $perm, string $resource, AclContext $context): bool
    {
        return $this->evaluator->hasResourcePermission($perm, $resource, $context->roles, $this->snapshot);
    }

    // -------------------------------------------------------------------------
    // Role checks
    // -------------------------------------------------------------------------

    public function hasRole(string $role, AclContext $context): bool
    {
        return in_array($role, $context->roles, true);
    }

    public function hasRoleOrHigher(string $role, AclContext $context): bool
    {
        if ($this->hasRole($role, $context)) {
            return true;
        }

        foreach ($context->roles as $userRole) {
            if ($this->hierarchy->isHigherRole($userRole, $role, $this->snapshot->parentRoleNames)) {
                return true;
            }
        }

        return false;
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

    public function hasAnyRoleOrHigher(array $roles, AclContext $context): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRoleOrHigher($role, $context)) {
                return true;
            }
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Ancestry (pure, no DB)
    // -------------------------------------------------------------------------

    public function getAncestry(string $role): array
    {
        return $this->hierarchy->getAncestry($role, $this->snapshot->parentRoleNames);
    }

    public function isHigherRole(string $role, string $referenced): ?bool
    {
        return $this->hierarchy->isHigherRole($role, $referenced, $this->snapshot->parentRoleNames);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getSnapshot(): AclSnapshot
    {
        return $this->snapshot;
    }

    public function getHierarchyResolver(): RoleHierarchyResolver
    {
        return $this->hierarchy;
    }

    public function getPermissionEvaluator(): PermissionEvaluator
    {
        return $this->evaluator;
    }
}
