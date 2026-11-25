<?php

namespace Boctulus\Simplerest\Core\Interfaces;

use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;
use Boctulus\Simplerest\Core\Security\Engine\AclEngine;

/**
 * Policy-query contract: inspect the constructed ACL policy.
 * Zero dependency on auth(), request(), or DB.
 * All methods are pure reads over the in-memory policy state.
 */
interface IAclPolicy
{
    public function getGuest(): string;

    public function getRegistered(): string;

    public function getRoleName($role_id = null);

    public function getRoleId(string $role_name);

    public function roleExists(string $role_name): bool;

    public function getEveryPossibleRole(): array;

    public function getEveryPossibleSpPermissions(): array;

    public function getRolePermissions(string $role_name = null);

    public function getResourcePermissions(string $role, string $resource, $op_type = null): array;

    public function getAncestry(string $role): array;

    public function isHigherRole(string $role, string $referenced_role): ?bool;

    /** Returns a pure immutable snapshot of the current policy (no auth, no DB). */
    public function getSnapshot(): AclSnapshot;

    /** Returns a pure engine seeded with the current policy snapshot. */
    public function getEngine(): AclEngine;
}
