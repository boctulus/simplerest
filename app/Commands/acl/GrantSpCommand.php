<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class GrantSpCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'grant-sp';
        $this->description = 'Otorga un special permission individual a un usuario (INSERT user_sp_permissions)';
        $this->aliases     = [];
        $this->examples    = [
            'php com acl grant-sp --email=user@example.com --perm=impersonate',
            'php com acl grant-sp --email=user@example.com --perm=impersonate --dry-run',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'perm'],
            'optional' => [],
            'flags'    => ['dry-run'],
            'options'  => [
                'email'   => ['describe' => 'Email del usuario'],
                'perm'    => ['describe' => 'Nombre del special permission (ver: php com acl list-sp)'],
                'dry-run' => ['describe' => 'Mostrar la acción sin ejecutarla'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email  = $this->opt($parsed, 'email');
        $perm   = $this->opt($parsed, 'perm');
        $dryRun = $this->opt($parsed, 'dry_run', false);

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $sp = $this->getSpPermByName($perm);
        if (!$sp) {
            echo "✗ Special permission '{$perm}' no existe. Use 'php com acl list-sp' para ver los disponibles.\n";
            return;
        }

        $uid  = $user[$this->idField];
        $spId = $sp['id'];

        $exists = $this->withDb(fn() =>
            DB::table('user_sp_permissions')
                ->where(['user_id' => $uid, 'sp_permission_id' => $spId])
                ->first()
        );

        if ($exists) {
            echo "⚠ El usuario ya tiene el special permission '{$perm}'.\n";
            return;
        }

        if ($dryRun) {
            $this->printDryRun("INSERT user_sp_permissions (user_id={$uid}, sp_permission_id={$spId}) -- perm: {$perm}");
            return;
        }

        $this->withDb(fn() =>
            DB::table('user_sp_permissions')->insert([
                'user_id'          => $uid,
                'sp_permission_id' => $spId,
                'created_by'       => $uid,
                'created_at'       => date('Y-m-d H:i:s'),
            ])
        );

        echo "✓ Special permission '{$perm}' otorgado a {$email}.\n";
    }
}
