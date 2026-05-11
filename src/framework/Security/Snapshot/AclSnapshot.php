<?php

namespace Boctulus\Simplerest\Core\Security\Snapshot;

final class AclSnapshot
{
    /**
     * @param array $rolePerms        ['role' => ['role_id'=>x, 'sp_permissions'=>[], 'tb_permissions'=>[]]]
     * @param array $parentRoleNames  ['child_role' => 'parent_role']
     * @param array $validSpPerms     all recognized special permission names
     */
    public function __construct(
        public readonly array  $rolePerms,
        public readonly array  $parentRoleNames,
        public readonly array  $validSpPerms,
        public readonly string $guestName      = 'guest',
        public readonly string $registeredName = 'registered',
    ) {}
}
