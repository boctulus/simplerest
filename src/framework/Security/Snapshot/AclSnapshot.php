<?php

namespace Boctulus\Simplerest\Core\Security\Snapshot;

final class AclSnapshot
{
    /**
     * @param array $rolePerms              ['role' => ['role_id'=>x, 'sp_permissions'=>[], 'tb_permissions'=>[]]]
     * @param array $parentRoleNames        ['child_role' => 'parent_role']
     * @param array $validSpPerms           all recognized special permission names
     * @param array $effectiveAllows        ['role' => ['resource' => ['action' => true]]] compiled per-role allows
     * @param array $effectiveDenies        ['role' => ['resource' => ['action' => true]]] compiled per-role internal deny cache (replacement semantics)
     * @param array $denyRolePerms          ['role' => ['tb'=> ['resource'=>['action'=>true]], 'sp'=>['perm'=>true]]] explicit role-level deny rules
     * @param array $permissionExplanations ['role.resource.action' => [...]] deterministic per-role explanations for admin frontend
     */
    public function __construct(
        public readonly array  $rolePerms,
        public readonly array  $parentRoleNames,
        public readonly array  $validSpPerms,
        public readonly string $guestName              = 'guest',
        public readonly string $registeredName         = 'registered',
        public readonly array  $effectiveAllows        = [],
        public readonly array  $effectiveDenies        = [],
        public readonly array  $denyRolePerms          = [],
        public readonly array  $permissionExplanations = [],
    ) {}
}
