<?php

use Boctulus\Simplerest\Core\Libs\DB;

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
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'perm', 'resource'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario'],
                'perm'     => ['describe' => 'Acción a explicar: show|list|create|update|delete|show_all|list_all'],
                'resource' => ['describe' => 'Recurso/tabla'],
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
        $acl          = $ctx['acl'];
        $context      = $ctx['context'];
        $roles        = $ctx['roles'];
        $userSpPerms  = $ctx['userSpPerms'];
        $userTbPerms  = $ctx['userTbPerms'];
        $userDenyPerms = $ctx['userDenyPerms'];

        $engine   = $acl->getEngine();
        $snapshot = $acl->getSnapshot();
        $chain    = [];

        // --- Layer 1: Role-level grants ---
        foreach ($roles as $roleName) {
            $rolePerms = $snapshot->rolePerms[$roleName] ?? [];

            // Explicit role tb_permissions
            $tbPerms = $rolePerms['tb_permissions'][$resource] ?? [];
            if (in_array($perm, $tbPerms)) {
                $chain[] = ['layer' => 'ROLE_GRANT', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => "tb_permissions[{$resource}]"];
            }

            // Wildcard via sp_permissions
            $sp = $rolePerms['sp_permissions'] ?? [];
            $writeActions = ['create', 'update', 'delete'];
            $readActions  = ['show', 'list', 'show_all', 'list_all'];

            if (in_array('write_all', $sp) && in_array($perm, $writeActions)) {
                $chain[] = ['layer' => 'WILDCARD', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => 'write_all'];
            }
            if (in_array('read_all', $sp) && in_array($perm, $readActions)) {
                $chain[] = ['layer' => 'WILDCARD', 'source' => "role:{$roleName}", 'effect' => 'allow', 'detail' => 'read_all'];
            }
        }

        // --- Layer 2: User sp wildcards ---
        $writeActions = ['create', 'update', 'delete'];
        $readActions  = ['show', 'list', 'show_all', 'list_all'];

        if (in_array('write_all', $userSpPerms) && in_array($perm, $writeActions)) {
            $chain[] = ['layer' => 'USER_WILDCARD', 'source' => 'user_sp_permissions', 'effect' => 'allow', 'detail' => 'write_all'];
        }
        if (in_array('read_all', $userSpPerms) && in_array($perm, $readActions)) {
            $chain[] = ['layer' => 'USER_WILDCARD', 'source' => 'user_sp_permissions', 'effect' => 'allow', 'detail' => 'read_all'];
        }

        // --- Layer 3: user_tb_permissions (replacement override) ---
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

        // --- Layer 4: Deny rules (highest precedence) ---
        if (!empty($userDenyPerms[$resource][$perm])) {
            $chain[] = ['layer' => 'USER_DENY', 'source' => 'user_deny_permissions', 'effect' => 'deny', 'detail' => "{$resource}.{$perm}"];
        }

        // --- Final decision ---
        $final   = $engine->can($context, $perm, $resource);
        $hasConflict = false;
        $hasAllow    = false;
        $hasDeny     = false;
        foreach ($chain as $entry) {
            if ($entry['effect'] === 'allow') $hasAllow = true;
            if ($entry['effect'] === 'deny')  $hasDeny  = true;
        }
        $hasConflict = $hasAllow && $hasDeny;

        // --- Output ---
        echo "\n";
        echo str_repeat('─', 55) . "\n";
        echo "explain: {$email} | {$resource}.{$perm}\n";
        echo str_repeat('─', 55) . "\n";
        echo "Precedencia: DENY > USER_TB_OVERRIDE > USER_WILDCARD > WILDCARD > ROLE_GRANT\n\n";

        if (empty($chain)) {
            echo "  (ninguna capa aplica — acceso denegado por defecto)\n";
        } else {
            foreach ($chain as $i => $entry) {
                $icon   = $entry['effect'] === 'allow' ? '✓' : '✗';
                $marker = ($i === count($chain) - 1) ? ' ← winner' : '';
                printf("  [%s] %-18s %-8s %s%s\n",
                    strtoupper($entry['effect'][0]) . $entry['effect'][1],
                    $entry['layer'],
                    $icon,
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
