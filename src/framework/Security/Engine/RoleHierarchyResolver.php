<?php

namespace Boctulus\Simplerest\Core\Security\Engine;

use Boctulus\Simplerest\Core\Security\Contracts\RoleHierarchyResolverInterface;

final class RoleHierarchyResolver implements RoleHierarchyResolverInterface
{
    /**
     * Returns all ancestor roles walking up the inheritance chain.
     * e.g. superadmin -> admin -> registered -> guest  returns [admin, registered, guest]
     */
    public function getAncestry(string $role, array $parentRoleNames): array
    {
        $ancestors = [];
        $current   = $role;

        while (isset($parentRoleNames[$current])) {
            $current     = $parentRoleNames[$current];
            $ancestors[] = $current;
        }

        return $ancestors;
    }

    /**
     * Returns true  if $role is higher (more privileged) than $referenced.
     * Returns false if $role is equal or lower.
     * Returns null  if the relationship cannot be determined (different branches).
     */
    public function isHigherRole(string $role, string $referenced, array $parentRoleNames): ?bool
    {
        if ($role === $referenced) {
            return false;
        }

        // If $role appears in $referenced's ancestry → $role is an ancestor → lower
        if (in_array($role, $this->getAncestry($referenced, $parentRoleNames), true)) {
            return false;
        }

        // If $referenced appears in $role's ancestry → $role is a descendant → higher
        if (in_array($referenced, $this->getAncestry($role, $parentRoleNames), true)) {
            return true;
        }

        return null;
    }
}
