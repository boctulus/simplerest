<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class RevokeSpCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'revoke-sp';
        $this->description = 'Revoca un special permission individual de un usuario (DELETE user_sp_permissions)';
        $this->aliases     = ['rm-sp'];
        $this->examples    = [
            'php com acl revoke-sp --email=user@example.com --perm=impersonate',
            'php com acl revoke-sp --email=user@example.com --perm=impersonate --dry-run',
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
                'perm'    => ['describe' => 'Nombre del special permission'],
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
            echo "✗ Special permission '{$perm}' no existe.\n";
            return;
        }

        $uid  = $user[$this->idField];
        $spId = $sp['id'];

        $exists = $this->withDb(fn() =>
            DB::table('user_sp_permissions')
                ->where(['user_id' => $uid, 'sp_permission_id' => $spId])
                ->first()
        );

        if (!$exists) {
            echo "⚠ El usuario no tiene el special permission '{$perm}'.\n";
            return;
        }

        if ($dryRun) {
            $this->printDryRun("DELETE user_sp_permissions WHERE user_id={$uid} AND sp_permission_id={$spId} -- perm: {$perm}");
            return;
        }

        $this->withDb(fn() =>
            DB::table('user_sp_permissions')
                ->where(['user_id' => $uid, 'sp_permission_id' => $spId])
                ->delete()
        );

        echo "✓ Special permission '{$perm}' revocado de {$email}.\n";
    }
}
