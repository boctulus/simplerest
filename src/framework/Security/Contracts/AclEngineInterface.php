<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;

interface AclEngineInterface
{
    public function can(AclContext $context, string $perm, string $resource): bool;

    public function hasSpecialPermission(string $perm, AclContext $context): bool;

    public function hasResourcePermission(string $perm, string $resource, AclContext $context): bool;

    public function hasRole(string $role, AclContext $context): bool;

    public function hasRoleOrHigher(string $role, AclContext $context): bool;

    public function hasAnyRole(array $roles, AclContext $context): bool;

    public function hasAnyRoleOrHigher(array $roles, AclContext $context): bool;
}
