<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class ReplaceTbCommand extends BaseAclCommand
{
    public string $group = 'acl';

    private const ACTION_FIELDS = [
        'list_all' => 'can_list_all',
        'show_all' => 'can_show_all',
        'list'     => 'can_list',
        'show'     => 'can_show',
        'create'   => 'can_create',
        'update'   => 'can_update',
        'delete'   => 'can_delete',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'replace-tb';
        $this->description = '⚠ DESTRUCTIVO: Reemplaza TODOS los permisos de tabla para un usuario (replacement semantics). Los permisos no listados en --perms= quedan en NULL.';
        $this->aliases     = [];
        $this->examples    = [
            'php com acl replace-tb --email=user@example.com --table=products --perms=show,list,create --dry-run',
            'php com acl replace-tb --email=user@example.com --table=products --perms=show,list,create',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'table', 'perms'],
            'optional' => [],
            'flags'    => ['dry-run'],
            'options'  => [
                'email'   => ['describe' => 'Email del usuario'],
                'table'   => ['describe' => 'Nombre de la tabla/recurso'],
                'perms'   => ['describe' => 'Lista CSV de permisos: show,list,create,update,delete,show_all,list_all (reemplaza TODO el set actual)'],
                'dry-run' => ['describe' => 'Mostrar la acción sin ejecutarla (MUY recomendado antes de ejecutar)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email  = $this->opt($parsed, 'email');
        $table  = $this->opt($parsed, 'table');
        $perms  = $this->opt($parsed, 'perms');
        $dryRun = $this->opt($parsed, 'dry_run', false);

        $requested = array_map('trim', explode(',', $perms));
        $expanded  = [];
        foreach ($requested as $p) {
            $actions = $this->expandPerm($p);
            if ($actions === null) {
                echo "✗ Permiso inválido: '{$p}'.\n";
                return;
            }
            $expanded = array_merge($expanded, $actions);
        }
        $expanded = array_unique($expanded);

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $uid = $user[$this->idField];

        // Build full replacement set (all fields NULL except requested)
        $data = array_fill_keys(array_values(self::ACTION_FIELDS), null);
        foreach ($expanded as $action) {
            $data[self::ACTION_FIELDS[$action]] = 1;
        }

        if ($dryRun) {
            $preview = [];
            foreach (self::ACTION_FIELDS as $action => $field) {
                $preview[] = $action . ': ' . (in_array($action, $expanded) ? '✓' : '✗');
            }
            echo "  [dry-run] replace-tb {$table} para {$email}:\n";
            foreach ($preview as $line) {
                echo "    {$line}\n";
            }
            return;
        }

        $existing = $this->withDb(fn() =>
            DB::table('user_tb_permissions')
                ->where(['user_id' => $uid, 'tb' => $table])
                ->first()
        );

        if ($existing) {
            $data['updated_by'] = $uid;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->withDb(fn() =>
                DB::table('user_tb_permissions')
                    ->where(['user_id' => $uid, 'tb' => $table])
                    ->update($data)
            );
        } else {
            $data['user_id']    = $uid;
            $data['tb']         = $table;
            $data['created_by'] = $uid;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->withDb(fn() => DB::table('user_tb_permissions')->insert($data));
        }

        echo "✓ Permisos de '{$table}' para {$email} reemplazados a: " . implode(', ', $expanded) . "\n";
    }

    private function expandPerm(string $perm): ?array
    {
        $shorthands = [
            'read'  => ['show', 'list'],
            'write' => ['create', 'update', 'delete'],
        ];

        if (isset($shorthands[$perm])) {
            return $shorthands[$perm];
        }

        if (isset(self::ACTION_FIELDS[$perm])) {
            return [$perm];
        }

        return null;
    }
}
