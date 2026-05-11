<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;

/**
 * Tree-based role comparison utility.
 *
 * These methods compare roles by lineage, NOT by effective permissions.
 * Use only in simple RBAC scenarios with a single linear inheritance chain.
 * For real authorization use AclEngineInterface::roleDominates() instead.
 */
interface RoleHierarchyServiceInterface
{
    public function getAncestry(string $role): array;

    /**
     * Returns true  if $role descends from $referenced (more specific/privileged in lineage).
     * Returns false if $role is equal or an ancestor of $referenced.
     * Returns null  if no lineage relationship exists (different branches).
     */
    public function isHigherRole(string $role, string $referenced): ?bool;

    public function hasRoleOrHigher(string $role, AclContext $context): bool;

    public function hasAnyRoleOrHigher(array $roles, AclContext $context): bool;
}
