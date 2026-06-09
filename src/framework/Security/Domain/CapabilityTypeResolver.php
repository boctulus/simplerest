<?php

namespace Boctulus\Simplerest\Core\Security\Domain;

use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

final class CapabilityTypeResolver
{
    const RESOURCE  = 'resource';
    const SYSTEM_SP = 'system_sp';
    const DOMAIN_SP = 'domain_sp';
    const UNKNOWN   = 'unknown';

    /**
     * Resolve type and canonical form of a capability.
     *
     * Returns array with keys:
     *   type:       RESOURCE | SYSTEM_SP | DOMAIN_SP | UNKNOWN
     *   capability: canonical string (e.g. "products.create", "cashbox.open", "impersonate")
     *   resource:   (RESOURCE only) resource name
     *   action:     (RESOURCE only) action name
     */
    public static function resolve(
        string      $perm,
        ?string     $resource,
        AclSnapshot $snapshot,
        array       $dbSpNames
    ): array {
        if ($resource !== null) {
            return [
                'type'       => self::RESOURCE,
                'resource'   => $resource,
                'action'     => $perm,
                'capability' => "{$resource}.{$perm}",
            ];
        }

        if (in_array($perm, $snapshot->validSpPerms, true)) {
            return [
                'type'       => self::SYSTEM_SP,
                'capability' => $perm,
            ];
        }

        if (in_array($perm, $dbSpNames, true) || str_contains($perm, '.')) {
            return [
                'type'       => self::DOMAIN_SP,
                'capability' => $perm,
            ];
        }

        return [
            'type'       => self::UNKNOWN,
            'capability' => $perm,
        ];
    }

    /**
     * Extract all known resource names from snapshot across all roles.
     */
    public static function knownResources(AclSnapshot $snapshot): array
    {
        $resources = [];
        foreach ($snapshot->rolePerms as $perms) {
            foreach (array_keys($perms['tb_permissions'] ?? []) as $res) {
                $resources[$res] = true;
            }
        }
        return array_keys($resources);
    }

    /**
     * Closest match from candidates for typo suggestions (max distance 3).
     */
    public static function suggest(string $needle, array $candidates): ?string
    {
        $best = null;
        $min  = PHP_INT_MAX;
        foreach ($candidates as $c) {
            $d = levenshtein($needle, $c);
            if ($d < $min && $d <= 3) {
                $min  = $d;
                $best = $c;
            }
        }
        return $best;
    }
}
