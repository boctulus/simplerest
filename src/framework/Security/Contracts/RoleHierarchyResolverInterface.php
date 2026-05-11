<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

interface RoleHierarchyResolverInterface
{
    public function getAncestry(string $role, array $parentRoleNames): array;

    public function isHigherRole(string $role, string $referenced, array $parentRoleNames): ?bool;
}
