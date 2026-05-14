<?php

namespace Boctulus\Simplerest\Core\Security\Compiler;

use Boctulus\Simplerest\Core\Security\Explanation\PermissionExplanation;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

/**
 * Resolves the ACL policy graph into hash-map effective permissions for O(1)
 * runtime evaluation.
 *
 * - `compileRoles()` produces a per-role view consumed by the admin frontend.
 * - `compileForUser()` produces a per-user view consumed by the engine fast path.
 *
 * Pure class: no DB, no auth(), no framework dependency.
 */
final class EffectivePermissionCompiler
{
    private const READ_ACTIONS  = ['show', 'list', 'show_all', 'list_all'];
    private const WRITE_ACTIONS = ['create', 'update', 'delete'];

    /**
     * Per-role compilation. Inheritance is already flattened at build-time
     * via Acl::addInherit() so we only re-shape into hash maps and merge
     * explicit role-level deny rules.
     *
     * @return array{allows: array, denies: array, explanations: array}
     */
    public function compileRoles(array $rolePerms, array $denyRolePerms, array $validSpPerms): array
    {
        $allows       = [];
        $denies       = [];
        $explanations = [];

        foreach ($rolePerms as $role => $rp) {
            foreach (($rp['tb_permissions'] ?? []) as $resource => $actions) {
                foreach ($actions as $action) {
                    $allows[$role][$resource][$action] = true;
                    $explanations["{$role}.{$resource}.{$action}"] = (new PermissionExplanation(
                        resource: $resource,
                        action:   $action,
                        granted:  true,
                        source:   PermissionExplanation::SOURCE_ROLE . ":{$role}",
                        mode:     PermissionExplanation::MODE_DIRECT,
                    ))->toArray();
                }
            }

            foreach (($rp['sp_permissions'] ?? []) as $sp) {
                $allows[$role]['__sp__'][$sp] = true;
            }

            $drp = $denyRolePerms[$role] ?? [];

            foreach (($drp['tb'] ?? []) as $resource => $actions) {
                foreach ($actions as $action => $_) {
                    $denies[$role][$resource][$action] = true;
                    $explanations["{$role}.{$resource}.{$action}"] = (new PermissionExplanation(
                        resource: $resource,
                        action:   $action,
                        granted:  false,
                        source:   PermissionExplanation::SOURCE_DENY_ROLE . ":{$role}",
                        mode:     PermissionExplanation::MODE_DENY,
                    ))->toArray();
                }
            }

            foreach (($drp['sp'] ?? []) as $sp => $_) {
                $denies[$role]['__sp__'][$sp] = true;
            }
        }

        return [
            'allows'       => $allows,
            'denies'       => $denies,
            'explanations' => $explanations,
        ];
    }

    /**
     * Per-user compilation. Applies, in order:
     *
     *   1. Aggregate role-derived allows (tb + sp).
     *   2. Additive user sp permissions.
     *   3. Expand read_all / write_all into wildcard '*' allows.
     *   4. user_tb_permissions REPLACEMENT for the affected resource.
     *   5. Role-level explicit denies.
     *   6. User-level explicit denies (beat everything including read_all).
     *
     * @param array<int|string,string> $roles
     * @param string[]                 $userSpPerms     additive
     * @param array<string,int>        $userTbPerms     packed bitmask per resource
     * @param array<string,array>      $userDenyPerms   ['resource' => ['action' => true]]
     * @param array<string,true>       $userDenySpPerms ['perm' => true]
     *
     * @return array{allow: array, deny: array}
     */
    public function compileForUser(
        AclSnapshot $snapshot,
        array       $roles,
        array       $userSpPerms     = [],
        array       $userTbPerms     = [],
        array       $userDenyPerms   = [],
        array       $userDenySpPerms = []
    ): array {
        $allow = [];
        $deny  = [];

        // 1. role-derived allows
        foreach ($roles as $role) {
            $rp = $snapshot->rolePerms[$role] ?? null;
            if ($rp === null) {
                continue;
            }

            foreach (($rp['tb_permissions'] ?? []) as $resource => $actions) {
                foreach ($actions as $action) {
                    $allow[$resource][$action] = true;
                }
            }

            foreach (($rp['sp_permissions'] ?? []) as $sp) {
                $allow['__sp__'][$sp] = true;
            }
        }

        // 2. additive user sp perms
        foreach ($userSpPerms as $sp) {
            $allow['__sp__'][$sp] = true;
        }

        // 3. expand read_all / write_all into wildcard '*'
        if (isset($allow['__sp__']['read_all'])) {
            foreach (self::READ_ACTIONS as $a) {
                $allow['*'][$a] = true;
            }
        }
        if (isset($allow['__sp__']['write_all'])) {
            foreach (self::WRITE_ACTIONS as $a) {
                $allow['*'][$a] = true;
            }
        }

        // 4. user_tb_permissions REPLACEMENT semantics
        foreach ($userTbPerms as $resource => $packed) {
            $allow[$resource] = TbPermissionBits::unpackGranted((int) $packed);
        }

        // 5. role-level explicit denies (from snapshot)
        foreach ($roles as $role) {
            $drp = $snapshot->denyRolePerms[$role] ?? [];

            foreach (($drp['tb'] ?? []) as $resource => $actions) {
                foreach ($actions as $action => $_) {
                    $deny[$resource][$action] = true;
                }
            }
            foreach (($drp['sp'] ?? []) as $sp => $_) {
                $deny['__sp__'][$sp] = true;
            }
        }

        // 6. user-level explicit denies
        foreach ($userDenyPerms as $resource => $actions) {
            foreach ($actions as $action => $_) {
                $deny[$resource][$action] = true;
            }
        }
        foreach ($userDenySpPerms as $sp => $_) {
            $deny['__sp__'][$sp] = true;
        }

        return ['allow' => $allow, 'deny' => $deny];
    }
}
