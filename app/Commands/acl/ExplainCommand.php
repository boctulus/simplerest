<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Security\Domain\CapabilityTypeResolver;

require_once __DIR__ . '/BaseAclCommand.php';

class ExplainCommand extends BaseAclCommand
{
    public string $group = 'acl';

    private const ACTION_BITS = [
        'list_all' => 64,
        'show_all' => 32,
        'list'     => 16,
        'show'     => 8,
        'create'   => 4,
        'update'   => 2,
        'delete'   => 1,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'explain';
        $this->description = 'Muestra el resolution chain completo para un permiso de un usuario. Precedencia: DENY > USER_GRANT > ROLE_GRANT';
        $this->aliases     = [];
        $this->examples    = [
            'php com acl explain --email=user@example.com --perm=delete --resource=products',
            'php com acl explain --email=user@example.com --perm=impersonate',
            'php com acl explain --email=user@example.com --perm=cashbox.open',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'perm'],
            'optional' => ['resource'],
            'flags'    => [],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario'],
                'perm'     => ['describe' => 'Acción CRUD (con --resource), SP del sistema (impersonate) o domain capability (cashbox.open)'],
                'resource' => ['describe' => 'Recurso/tabla (requerido para CRUD, omitir para SPs y domain capabilities)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email    = $this->opt($parsed, 'email');
        $perm     = $this->opt($parsed, 'perm');
        $resource = $this->opt($parsed, 'resource');

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $uid = $user[$this->idField];
        $ctx = $this->buildUserAclContext($uid);

        /** @var \Boctulus\FineGrainedACL\Acl $acl */
        $acl           = $ctx['acl'];
        $context       = $ctx['context'];
        $roles         = $ctx['roles'];
        $userSpPerms   = $ctx['userSpPerms'];
        $userTbPerms   = $ctx['userTbPerms'];
        $userDenyPerms = $ctx['userDenyPerms'];

        $engine    = $acl->getEngine();
        $snapshot  = $acl->getSnapshot();
        $dbSpPerms = $this->dbSpPerms();

        $resolved = CapabilityTypeResolver::resolve($perm, $resource, $snapshot, $dbSpPerms);

        switch ($resolved['type']) {

            case CapabilityTypeResolver::RESOURCE:
                $known = CapabilityTypeResolver::knownResources($snapshot);
                if (!in_array($resolved['resource'], $known, true)) {
                    $suggestion = CapabilityTypeResolver::suggest($resolved['resource'], $known);
                    echo "✗ Recurso desconocido: '{$resolved['resource']}'\n";
                    if ($suggestion) {
                        echo "  ¿Quisiste decir: {$suggestion}?\n";
                    }
                    return;
                }
                $this->explainResource($email, $resolved['action'], $resolved['resource'], $roles, $userSpPerms, $userTbPerms, $userDenyPerms, $engine, $snapshot, $context);
                break;

            case CapabilityTypeResolver::SYSTEM_SP:
                $this->explainSp($email, $resolved['capability'], 'system', $roles, $userSpPerms, $engine, $snapshot, $context);
                break;

            case CapabilityTypeResolver::DOMAIN_SP:
                if (!in_array($resolved['capability'], $dbSpPerms, true)) {
                    $suggestion = CapabilityTypeResolver::suggest($resolved['capability'], $dbSpPerms);
                    echo "✗ Domain capability desconocida: '{$resolved['capability']}'\n";
                    if ($suggestion) {
                        echo "  ¿Quisiste decir: {$suggestion}?\n";
                    }
                    return;
                }
                $this->explainSp($email, $resolved['capability'], 'domain', $roles, $userSpPerms, $engine, $snapshot, $context);
                break;

            default:
                $allCaps    = array_merge($snapshot->validSpPerms, $dbSpPerms);
                $suggestion = CapabilityTypeResolver::suggest($perm, $allCaps);
                echo "✗ Capability inválida: '{$perm}'\n";
                if ($suggestion) {
                    echo "  ¿Quisiste decir: {$suggestion}?\n";
                }
                break;
        }
    }

    // ── Resource capability explain ────────────────────────────────────────

    private function explainResource(
        string $email,
        string $perm,
        string $resource,
        array  $roles,
        array  $userSpPerms,
        array  $userTbPerms,
        array  $userDenyPerms,
        $engine,
        $snapshot,
        $context
    ): void {
        $chain = [];

        foreach ($roles as $roleName) {
            $rolePerms = $snapshot->rolePerms[$roleName] ?? [];

            $tbPerms = $rolePerms['tb_permissions'][$resource] ?? [];
            if (in_array($perm, $tbPerms)) {
                $chain[] = ['layer' => 'ROLE_GRANT', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => "tb_permissions[{$resource}]"];
            }

            $sp           = $rolePerms['sp_permissions'] ?? [];
            $writeActions = ['create', 'update', 'delete'];
            $readActions  = ['show', 'list', 'show_all', 'list_all'];

            if (in_array('write_all', $sp) && in_array($perm, $writeActions)) {
                $chain[] = ['layer' => 'WILDCARD', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => 'write_all'];
            }
            if (in_array('read_all', $sp) && in_array($perm, $readActions)) {
                $chain[] = ['layer' => 'WILDCARD', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => 'read_all'];
            }
        }

        $writeActions = ['create', 'update', 'delete'];
        $readActions  = ['show', 'list', 'show_all', 'list_all'];

        if (in_array('write_all', $userSpPerms) && in_array($perm, $writeActions)) {
            $chain[] = ['layer' => 'USER_WILDCARD', 'source' => 'user_sp_permissions', 'effect' => 'allow', 'detail' => 'write_all'];
        }
        if (in_array('read_all', $userSpPerms) && in_array($perm, $readActions)) {
            $chain[] = ['layer' => 'USER_WILDCARD', 'source' => 'user_sp_permissions', 'effect' => 'allow', 'detail' => 'read_all'];
        }

        if (isset($userTbPerms[$resource])) {
            $packed  = $userTbPerms[$resource];
            $bit     = self::ACTION_BITS[$perm] ?? 0;
            $allowed = $bit > 0 && ($packed & $bit);
            $chain[] = [
                'layer'  => 'USER_TB_OVERRIDE',
                'source' => 'user_tb_permissions',
                'effect' => $allowed ? 'allow' : 'deny',
                'detail' => "replacement set for {$resource}",
            ];
        }

        if (!empty($userDenyPerms[$resource][$perm])) {
            $chain[] = ['layer' => 'USER_DENY', 'source' => 'user_deny_permissions', 'effect' => 'deny', 'detail' => "{$resource}.{$perm}"];
        }

        $final       = $engine->can($context, $perm, $resource);
        $hasAllow    = false;
        $hasDeny     = false;
        foreach ($chain as $entry) {
            if ($entry['effect'] === 'allow') $hasAllow = true;
            if ($entry['effect'] === 'deny')  $hasDeny  = true;
        }
        $hasConflict = $hasAllow && $hasDeny;

        $this->printChain(
            label:       "{$email} | {$resource}.{$perm}",
            chain:       $chain,
            final:       $final,
            hasConflict: $hasConflict,
            precedence:  'DENY > USER_TB_OVERRIDE > USER_WILDCARD > WILDCARD > ROLE_GRANT'
        );
    }

    // ── SP capability explain (system + domain) ────────────────────────────

    private function explainSp(
        string $email,
        string $perm,
        string $kind,
        array  $roles,
        array  $userSpPerms,
        $engine,
        $snapshot,
        $context
    ): void {
        $chain = [];

        foreach ($roles as $roleName) {
            $sp = $snapshot->rolePerms[$roleName]['sp_permissions'] ?? [];
            if (in_array($perm, $sp, true)) {
                $chain[] = ['layer' => 'ROLE_GRANT', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => "sp_permissions[{$perm}]"];
            }
        }

        if (in_array($perm, $userSpPerms, true)) {
            $chain[] = ['layer' => 'USER_SP_GRANT', 'source' => 'user_sp_permissions', 'effect' => 'allow', 'detail' => $perm];
        }

        // Deny check via engine internals (reflected in final result)
        $final = $engine->hasSpecialPermission($perm, $context);

        // If engine denies but chain has allows → explicit deny rule
        if (!$final && !empty(array_filter($chain, fn($e) => $e['effect'] === 'allow'))) {
            $chain[] = ['layer' => 'SP_DENY', 'source' => 'deny_rules', 'effect' => 'deny', 'detail' => $perm];
        }

        $hasAllow = !empty(array_filter($chain, fn($e) => $e['effect'] === 'allow'));
        $hasDeny  = !empty(array_filter($chain, fn($e) => $e['effect'] === 'deny'));

        $this->printChain(
            label:       "{$email} | {$kind}:{$perm}",
            chain:       $chain,
            final:       $final,
            hasConflict: $hasAllow && $hasDeny,
            precedence:  'DENY > USER_SP_GRANT > ROLE_GRANT'
        );
    }

    // ── Output ────────────────────────────────────────────────────────────

    private function printChain(
        string $label,
        array  $chain,
        bool   $final,
        bool   $hasConflict,
        string $precedence
    ): void {
        echo "\n";
        echo str_repeat('─', 55) . "\n";
        echo "explain: {$label}\n";
        echo str_repeat('─', 55) . "\n";
        echo "Precedencia: {$precedence}\n\n";

        if (empty($chain)) {
            echo "  (ninguna capa aplica — acceso denegado por defecto)\n";
        } else {
            foreach ($chain as $i => $entry) {
                $marker = ($i === count($chain) - 1) ? ' ← winner' : '';
                printf("  [%s] %-18s %-8s %s%s\n",
                    strtoupper($entry['effect'][0]) . $entry['effect'][1],
                    $entry['layer'],
                    $entry['effect'] === 'allow' ? '✓' : '✗',
                    $entry['source'] . ' (' . $entry['detail'] . ')',
                    $marker
                );
            }
        }

        echo str_repeat('─', 55) . "\n";
        $finalLabel = $final ? 'ALLOW' : 'DENY';
        $finalIcon  = $final ? '✓' : '✗';
        echo "Decisión final: {$finalIcon} {$finalLabel}";
        if ($hasConflict) {
            echo "  ⚠ (conflicto: deny ganó por precedencia)";
        }
        echo "\n" . str_repeat('─', 55) . "\n";
    }
}
