<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class RevokeTbCommand extends BaseAclCommand
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
        $this->command     = 'revoke-tb';
        $this->description = 'Quita un permiso incremental de tabla de un usuario (update can_* a NULL). Shorthand: read|write';
        $this->aliases     = ['rm-tb-perm'];
        $this->examples    = [
            'php com acl revoke-tb --email=user@example.com --table=products --perm=delete',
            'php com acl revoke-tb --email=user@example.com --table=products --perm=write --dry-run',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'table', 'perm'],
            'optional' => [],
            'flags'    => ['dry-run'],
            'options'  => [
                'email'   => ['describe' => 'Email del usuario'],
                'table'   => ['describe' => 'Nombre de la tabla/recurso'],
                'perm'    => ['describe' => 'Permiso: show|list|create|update|delete|show_all|list_all|read|write'],
                'dry-run' => ['describe' => 'Mostrar la acción sin ejecutarla'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email  = $this->opt($parsed, 'email');
        $table  = $this->opt($parsed, 'table');
        $perm   = $this->opt($parsed, 'perm');
        $dryRun = $this->opt($parsed, 'dry_run', false);

        $actions = $this->expandPerm($perm);
        if ($actions === null) {
            echo "✗ Permiso inválido: '{$perm}'.\n";
            return;
        }

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $uid = $user[$this->idField];

        $existing = $this->withDb(fn() =>
            DB::table('user_tb_permissions')
                ->where(['user_id' => $uid, 'tb' => $table])
                ->first()
        );

        if (!$existing) {
            echo "⚠ No hay permisos de tabla para '{$table}' en este usuario.\n";
            return;
        }

        if ($dryRun) {
            $fields = array_map(fn($a) => self::ACTION_FIELDS[$a] . '=NULL', $actions);
            $this->printDryRun("UPDATE user_tb_permissions SET " . implode(', ', $fields) . " WHERE user_id={$uid} AND tb={$table}");
            return;
        }

        $data = [];
        foreach ($actions as $action) {
            $data[self::ACTION_FIELDS[$action]] = null;
        }

        $this->withDb(fn() =>
            DB::table('user_tb_permissions')
                ->where(['user_id' => $uid, 'tb' => $table])
                ->update($data)
        );

        echo "✓ Permiso(s) '" . implode(', ', $actions) . "' revocado(s) en '{$table}' para {$email}.\n";
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
