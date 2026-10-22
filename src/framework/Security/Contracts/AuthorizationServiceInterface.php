<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;

/**
 * Capability-based authorization engine contract.
 *
 * Compares effective permission sets, NOT role lineage.
 * All decisions are deterministic given the same AclSnapshot + AclContext.
 */
interface AuthorizationServiceInterface
{
    // ── Per-action evaluation ──────────────────────────────────────────────

    public function can(AclContext $context, string $action, string $resource): bool;

    public function hasSpecialPermission(string $perm, AclContext $context): bool;

    public function hasResourcePermission(string $perm, string $resource, AclContext $context): bool;

    // ── Role membership (no hierarchy) ────────────────────────────────────

    public function hasRole(string $role, AclContext $context): bool;

    public function hasAnyRole(array $roles, AclContext $context): bool;

    // ── Capability / permission-set evaluation ────────────────────────────

    /**
     * True if the user satisfies ALL of the given permissions.
     * Permission format: "resource.action" or a special-permission name.
     * Examples: ["products.create", "users.list_all", "read_all"]
     */
    public function hasAllPermissions(AclContext $context, array $permissions): bool;

    /**
     * True if the user satisfies AT LEAST ONE of the given permissions.
     */
    public function hasAnyPermission(AclContext $context, array $permissions): bool;

    /**
     * True if the effective permission set of $candidateRole is a superset of
     * the effective permission set of $targetRole (set-theory dominance).
     * Does NOT use role lineage — pure permission comparison.
     */
    public function roleDominates(string $candidateRole, string $targetRole): bool;

    /**
     * Delegates authorization logic to a policy object.
     */
    public function satisfiesPolicy(AclContext $context, AuthorizationPolicyInterface $policy): bool;

    /**
     * True if an explicit DENY rule applies to ($resource, $action) for the
     * given context — at user-level (`AclContext::$userDenyPerms`) or
     * role-level (`AclSnapshot::$denyRolePerms`).
     *
     * Business-level deny: a true result here forces a final deny regardless
     * of any ALLOW rule (including read_all / write_all sentinels).
     */
    public function hasExplicitDeny(AclContext $context, string $action, string $resource): bool;
}
