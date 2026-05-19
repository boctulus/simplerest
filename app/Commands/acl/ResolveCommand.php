<?php

require_once __DIR__ . '/BaseAclCommand.php';

class ResolveCommand extends BaseAclCommand
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
        $this->command     = 'resolve';
        $this->description = 'Muestra los permisos efectivos compilados de un usuario (todos los layers)';
        $this->aliases     = ['effective'];
        $this->examples    = [
            'php com acl resolve user@example.com',
            'php com acl resolve --email=user@example.com --only=sp',
            'php com acl resolve --email=user@example.com --format=json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email', 'only', 'format'],
            'flags'    => [],
            'options'  => [
                'email'  => ['describe' => 'Email del usuario (o primer arg posicional)'],
                'only'   => ['describe' => 'Filtrar sección: roles | sp | tb | deny'],
                'format' => ['describe' => 'Formato: text (default) | json', 'default' => 'text'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $user = $this->requireUser($parsed);
        if (!$user) return;

        $only   = $this->opt($parsed, 'only');
        $format = $this->opt($parsed, 'format', 'text');

        $uid = $user[$this->idField];
        $ctx = $this->buildUserAclContext($uid);

        $acl          = $ctx['acl'];
        $roles        = $ctx['roles'];
        $userSpPerms  = $ctx['userSpPerms'];
        $userTbPerms  = $ctx['userTbPerms'];
        $userDenyPerms = $ctx['userDenyPerms'];
        $snapshot     = $acl->getSnapshot();

        // Compute effective sp (from roles + user grants)
        $effectiveSp = [];
        foreach ($roles as $role) {
            $sp = $snapshot->rolePerms[$role]['sp_permissions'] ?? [];
            foreach ($sp as $p) {
                $effectiveSp[$p] = 'ROLE_GRANT:' . $role;
            }
        }
        foreach ($userSpPerms as $p) {
            $effectiveSp[$p] = 'USER_GRANT';
        }

        // Compute effective tb (from roles + user overrides)
        $effectiveTb = [];
        foreach ($roles as $role) {
            $tb = $snapshot->rolePerms[$role]['tb_permissions'] ?? [];
            foreach ($tb as $table => $actions) {
                foreach ($actions as $a) {
                    $effectiveTb[$table][$a] = 'ROLE_GRANT:' . $role;
                }
            }
        }
        // Wildcard expansion
        if (isset($effectiveSp['write_all'])) {
            $source = $effectiveSp['write_all'];
            // mark wildcard — don't enumerate all tables
        }

        // User tb overrides (replacement semantics)
        foreach ($userTbPerms as $table => $packed) {
            $effectiveTb[$table] = [];
            foreach (self::ACTION_BITS as $action => $bit) {
                if ($packed & $bit) {
                    $effectiveTb[$table][$action] = 'USER_TB_OVERRIDE';
                }
            }
        }

        // Deny rules
        $denyDisplay = [];
        foreach ($userDenyPerms as $resource => $actions) {
            foreach ($actions as $action => $_) {
                $denyDisplay[] = "{$resource}.{$action}";
            }
        }

        // Output
        if ($format === 'json') {
            echo json_encode([
                'user'         => $user[$this->emailField],
                'roles'        => $roles,
                'sp_effective' => $effectiveSp,
                'tb_effective' => $effectiveTb,
                'deny_rules'   => $denyDisplay,
            ], JSON_PRETTY_PRINT) . "\n";
            return;
        }

        echo "\n";
        echo "Permisos efectivos de {$user[$this->emailField]}\n";
        echo str_repeat('─', 50) . "\n";

        if (!$only || $only === 'roles') {
            echo "Roles:\n";
            if (empty($roles)) {
                echo "  (sin roles)\n";
            } else {
                foreach ($roles as $r) {
                    echo "  • {$r}\n";
                }
            }
        }

        if (!$only || $only === 'sp') {
            echo "\nSpecial Permissions (efectivos):\n";
            if (empty($effectiveSp)) {
                echo "  (ninguno)\n";
            } else {
                foreach ($effectiveSp as $p => $source) {
                    echo "  • {$p}  [{$source}]\n";
                }
            }
            if (isset($effectiveSp['write_all']) || isset($effectiveSp['read_all'])) {
                echo "  ⚠ WILDCARD activo: acceso global a recursos según write_all/read_all\n";
            }
        }

        if (!$only || $only === 'tb') {
            echo "\nResource Permissions (tb):\n";
            if (empty($effectiveTb)) {
                echo "  (ninguno)\n";
            } else {
                foreach ($effectiveTb as $table => $actions) {
                    if (empty($actions)) {
                        echo "  {$table}: (ninguno)\n";
                    } else {
                        $parts = [];
                        foreach ($actions as $a => $src) {
                            $parts[] = "{$a}[{$src}]";
                        }
                        echo "  {$table}: " . implode(', ', $parts) . "\n";
                    }
                }
            }
        }

        if (!$only || $only === 'deny') {
            echo "\nDeny Rules (mayor precedencia):\n";
            if (empty($denyDisplay)) {
                echo "  (ninguna)\n";
            } else {
                foreach ($denyDisplay as $d) {
                    echo "  ✗ {$d}\n";
                }
            }
        }

        echo str_repeat('─', 50) . "\n";
    }
}
