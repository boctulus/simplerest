<?php

namespace Boctulus\Simplerest\Core\Security\Service;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Engine\AclEngine;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

class RoleHierarchyService
{
    public function __construct(
        private readonly AclSnapshot $snapshot,
    ) {}

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

    public function hasRolePermissionsOrHigher(AclContext $context, string $targetRole, AclEngine $engine): bool
    {
        foreach ($context->roles as $userRole) {
            if ($userRole === $targetRole || $this->isHigherRole($userRole, $targetRole) === true) {
                return true;
            }
        }

        $rp = $this->snapshot->rolePerms[$targetRole] ?? null;
        if ($rp === null) {
            return true;
        }

        $targetPerms = [];

        foreach ($rp['sp_permissions'] ?? [] as $sp) {
            $targetPerms[] = ['type' => 'sp', 'value' => $sp];
        }

        foreach ($rp['tb_permissions'] ?? [] as $resource => $actions) {
            foreach ($actions as $action) {
                $targetPerms[] = ['type' => 'tb', 'resource' => $resource, 'action' => $action];
            }
        }

        if (empty($targetPerms)) {
            return true;
        }

        foreach ($targetPerms as $perm) {
            if ($perm['type'] === 'sp') {
                if (!$engine->hasSpecialPermission($perm['value'], $context)) {
                    return false;
                }
            } else {
                if (!$engine->can($context, $perm['action'], $perm['resource'])) {
                    return false;
                }
            }
        }

        return true;
    }

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
