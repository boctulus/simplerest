<?php

namespace Boctulus\Simplerest\Modules\Security;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

/**
 * Read-only causality attribution over the ALREADY-compiled ACL artifact.
 *
 * It does NOT re-decide authorization. It explains the decision the v4 engine
 * already baked into AclContext::$compiledPermissions, walking the same
 * precedence order:
 *
 *     deny[resource][action] -> deny['*'] -> allow['*'] -> allow[resource]
 *
 * and attributing each matched layer to an orthogonal (origin, effect) pair:
 *
 *     origin: ROLE | USER | WILDCARD | DEFAULT
 *     effect: ALLOW | DENY
 *
 * Pure: no DB, no auth(), no framework dependency. Mirrors the design of
 * EffectivePermissionCompiler so it is unit-testable in isolation.
 */
final class AclResolutionExplainer
{
    private const READ_ACTIONS  = ['show', 'list', 'show_all', 'list_all'];
    private const WRITE_ACTIONS = ['create', 'update', 'delete'];

    public const ORIGIN_ROLE     = 'ROLE';
    public const ORIGIN_USER     = 'USER';
    public const ORIGIN_WILDCARD = 'WILDCARD';
    public const ORIGIN_DEFAULT  = 'DEFAULT';

    public const EFFECT_ALLOW = 'ALLOW';
    public const EFFECT_DENY  = 'DENY';

    /**
     * @return array{
     *   resource:string, action:string, result:string,
     *   resolution_path:array<int,array{origin:string,effect:string,source:string}>,
     *   final_decision:string, has_conflict:bool
     * }
     */
    public function explain(
        AclContext  $context,
        AclSnapshot $snapshot,
        string      $resource,
        string      $action
    ): array {
        $compiled = $context->compiledPermissions ?? ['allow' => [], 'deny' => []];
        $allow    = $compiled['allow'] ?? [];
        $deny     = $compiled['deny']  ?? [];

        $path        = [];
        $allowMatched = false;

        // ---- ALLOW layers (collected for trace + conflict detection) ----

        // role-derived allow (snapshot rolePerms still carries declared origin)
        foreach ($context->roles as $role) {
            $acts = $snapshot->rolePerms[$role]['tb_permissions'][$resource] ?? [];
            if (in_array($action, $acts, true)) {
                $path[] = $this->row(self::ORIGIN_ROLE, self::EFFECT_ALLOW, (string) $role);
                $allowMatched = true;
                break;
            }
        }

        // wildcard allow (read_all / write_all expanded to '*')
        if (isset($allow['*'][$action])) {
            $cap = in_array($action, self::READ_ACTIONS, true) ? 'read_all'
                 : (in_array($action, self::WRITE_ACTIONS, true) ? 'write_all' : '*');
            $path[] = $this->row(self::ORIGIN_WILDCARD, self::EFFECT_ALLOW, $cap);
            $allowMatched = true;
        }

        // user_tb_permissions REPLACEMENT allow
        if (isset($context->userTbPerms[$resource])
            && isset($allow[$resource][$action])) {
            $path[] = $this->row(self::ORIGIN_USER, self::EFFECT_ALLOW, 'user_tb_permissions');
            $allowMatched = true;
        }

        // ---- DENY layers (precedence: beat everything) ----

        $denied = false;

        foreach ($context->roles as $role) {
            if (isset($snapshot->denyRolePerms[$role]['tb'][$resource][$action])) {
                $path[]  = $this->row(self::ORIGIN_ROLE, self::EFFECT_DENY, (string) $role);
                $denied  = true;
            }
        }

        if (isset($context->userDenyPerms[$resource][$action])) {
            $path[]  = $this->row(self::ORIGIN_USER, self::EFFECT_DENY, "{$resource}.{$action}");
            $denied  = true;
        }

        // wildcard deny (rare) — present in the compiled deny map under '*'
        if (isset($deny['*'][$action])) {
            $path[]  = $this->row(self::ORIGIN_WILDCARD, self::EFFECT_DENY, $action);
            $denied  = true;
        }

        // ---- final decision (v4 engine semantics: DENY > ALLOW) ----

        $denyCompiled = isset($deny[$resource][$action]) || isset($deny['*'][$action]);

        if ($denied || $denyCompiled) {
            $result = 'deny';
        } elseif (isset($allow['*'][$action]) || isset($allow[$resource][$action])) {
            $result = 'allow';
        } else {
            $result = 'deny';
            if (!$path) {
                $path[] = $this->row(self::ORIGIN_DEFAULT, self::EFFECT_DENY, 'no matching policy');
            }
        }

        $hasConflict = $allowMatched && $result === 'deny';

        return [
            'resource'        => $resource,
            'action'          => $action,
            'result'          => $result,
            'resolution_path' => $path,
            'decisive'        => $this->decisive($path, $result),
            'final_decision'  => $result,
            'has_conflict'    => $hasConflict,
        ];
    }

    /**
     * Capability (special permission) attribution. Mirrors the resource flow
     * but over the compiled '__sp__' bucket.
     *
     * @return array{result:string, origin:string, source:string}
     */
    public function explainCapability(AclContext $context, string $sp): array
    {
        $compiled = $context->compiledPermissions ?? ['allow' => [], 'deny' => []];

        if (isset($compiled['deny']['__sp__'][$sp])) {
            $origin = isset($context->userDenySpPerms[$sp])
                ? self::ORIGIN_USER
                : self::ORIGIN_ROLE;
            $source = $origin === self::ORIGIN_USER ? 'user_deny_sp_permissions' : 'role';
            return ['result' => 'deny', 'origin' => $origin, 'source' => $source];
        }

        if (isset($compiled['allow']['__sp__'][$sp])) {
            $origin = in_array($sp, $context->userSpPerms, true)
                ? self::ORIGIN_USER
                : self::ORIGIN_ROLE;
            $source = $origin === self::ORIGIN_USER ? 'user_sp_permissions' : 'role';
            return ['result' => 'allow', 'origin' => $origin, 'source' => $source];
        }

        return ['result' => 'deny', 'origin' => self::ORIGIN_DEFAULT, 'source' => 'no matching policy'];
    }

    /**
     * The single layer that DETERMINED the final decision (engine precedence:
     * DENY > ALLOW; wildcard allow decides before resource allow). Used by the
     * effective grid so a denied cell shows the deny origin, not the first
     * matched allow.
     */
    private function decisive(array $path, string $result): array
    {
        $byOrigin = function (array $path, string $effect, string $origin) {
            foreach ($path as $row) {
                if ($row['effect'] === $effect && $row['origin'] === $origin) {
                    return $row;
                }
            }
            return null;
        };

        if ($result === 'deny') {
            foreach ([self::ORIGIN_USER, self::ORIGIN_ROLE, self::ORIGIN_WILDCARD] as $o) {
                $r = $byOrigin($path, self::EFFECT_DENY, $o);
                if ($r) {
                    return $r;
                }
            }
            foreach ($path as $row) {
                if ($row['effect'] === self::EFFECT_DENY) {
                    return $row;
                }
            }
            return $this->row(self::ORIGIN_DEFAULT, self::EFFECT_DENY, 'no matching policy');
        }

        foreach ([self::ORIGIN_WILDCARD, self::ORIGIN_USER, self::ORIGIN_ROLE] as $o) {
            $r = $byOrigin($path, self::EFFECT_ALLOW, $o);
            if ($r) {
                return $r;
            }
        }
        foreach ($path as $row) {
            if ($row['effect'] === self::EFFECT_ALLOW) {
                return $row;
            }
        }
        return $this->row(self::ORIGIN_DEFAULT, self::EFFECT_DENY, 'no matching policy');
    }

    private function row(string $origin, string $effect, string $source): array
    {
        return [
            'origin' => $origin,
            'effect' => $effect,
            'source' => $source,
        ];
    }
}
