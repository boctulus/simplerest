<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

interface PermissionEvaluatorInterface
{
    public function hasSpecialPermission(string $perm, AclContext $context, AclSnapshot $snapshot): bool;

    public function hasResourcePermission(string $perm, string $resource, array $roles, AclSnapshot $snapshot): bool;

    public function hasPermission(string $perm, string $resource, AclContext $context, AclSnapshot $snapshot): bool;
}
