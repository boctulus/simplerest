<?php

namespace Boctulus\Simplerest\Core\Security\Service;

use Boctulus\Simplerest\Core\Security\Contracts\RoleHierarchyServiceInterface;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Engine\RoleHierarchyResolver;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

/**
 * Tree-based role comparison utility.
 *
 * Compares roles by lineage (parent_role_names graph), not by effective permissions.
 * Valid only for simple RBAC with a single linear chain.
 * For real authorization correctness use AclEngine::roleDominates() instead.
 */
final class RoleHierarchyService implements RoleHierarchyServiceInterface
{
    private readonly RoleHierarchyResolver $resolver;

    public function __construct(
        private readonly AclSnapshot    $snapshot,
        ?RoleHierarchyResolver          $resolver = null,
    ) {
        $this->resolver = $resolver ?? new RoleHierarchyResolver();
    }

    public function getAncestry(string $role): array
    {
        return $this->resolver->getAncestry($role, $this->snapshot->parentRoleNames);
    }

    public function isHigherRole(string $role, string $referenced): ?bool
    {
        return $this->resolver->isHigherRole($role, $referenced, $this->snapshot->parentRoleNames);
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
}
