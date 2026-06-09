<?php

require_once __DIR__ . '/BaseAclCommand.php';

class InspectRoleCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'inspect-role';
        $this->description = 'Muestra la política compilada de un rol (source: acl.php)';
        $this->aliases     = ['ls-role-policy'];
        $this->examples    = [
            'php com acl inspect-role admin',
            'php com acl inspect-role --role=supervisor',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['role'],
            'flags'    => [],
            'options'  => [
                'role' => ['describe' => 'Nombre del rol (o primer arg posicional)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $roleName = $this->opt($parsed, 'role') ?? ($parsed['_positional'][0] ?? null);

        if (!$roleName) {
            echo "✗ Se requiere --role= o nombre del rol como argumento posicional.\n";
            $this->showUsage();
            return;
        }

        $acl = $this->loadAcl();

        if (!$acl->roleExists($roleName)) {
            echo "✗ Rol '{$roleName}' no existe en la política ACL.\n";
            return;
        }

        $perms = $acl->getRolePermissions($roleName);

        echo "\n";
        echo "SOURCE: acl.php (compiled policy)\n";
        echo str_repeat('─', 50) . "\n";
        echo "Rol:    {$roleName}\n";
        echo "ID:     " . ($perms['role_id'] ?? 'N/A') . "\n";

        // Inheritance
        $ancestry = $acl->getAncestry($roleName);
        if (!empty($ancestry)) {
            echo "Hereda: " . implode(' → ', $ancestry) . "\n";
        }

        // Special permissions
        echo str_repeat('─', 50) . "\n";
        echo "Special Permissions (capabilities):\n";
        $sp = $perms['sp_permissions'] ?? [];
        if (empty($sp)) {
            echo "  (ninguno)\n";
        } else {
            foreach ($sp as $p) {
                echo "  • {$p}\n";
            }
        }

        // Table permissions
        echo str_repeat('─', 50) . "\n";
        echo "Resource Permissions (tb_permissions):\n";
        $tb = $perms['tb_permissions'] ?? [];
        if (empty($tb)) {
            echo "  (ninguno)\n";
        } else {
            foreach ($tb as $table => $actions) {
                echo "  {$table}: " . implode(', ', $actions) . "\n";
            }
        }

        echo str_repeat('─', 50) . "\n";
    }
}
