<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class ClearTbCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'clear-tb';
        $this->description = '⚠ DESTRUCTIVO: Elimina TODOS los permisos de tabla individuales de un usuario para una tabla (DELETE user_tb_permissions)';
        $this->aliases     = ['rm-tb'];
        $this->examples    = [
            'php com acl clear-tb --email=user@example.com --table=products --dry-run',
            'php com acl clear-tb --email=user@example.com --table=products --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'table'],
            'optional' => [],
            'flags'    => ['dry-run', 'force'],
            'options'  => [
                'email'   => ['describe' => 'Email del usuario'],
                'table'   => ['describe' => 'Nombre de la tabla/recurso'],
                'dry-run' => ['describe' => 'Mostrar la acción sin ejecutarla'],
                'force'   => ['describe' => 'Requerido para confirmar la operación destructiva'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email  = $this->opt($parsed, 'email');
        $table  = $this->opt($parsed, 'table');
        $dryRun = $this->opt($parsed, 'dry_run', false);

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
            $this->printDryRun("DELETE user_tb_permissions WHERE user_id={$uid} AND tb={$table}");
            return;
        }

        if (!$this->requireConfirm($parsed)) return;

        $this->withDb(fn() =>
            DB::table('user_tb_permissions')
                ->where(['user_id' => $uid, 'tb' => $table])
                ->delete()
        );

        echo "✓ Permisos de '{$table}' eliminados para {$email}.\n";
    }
}
