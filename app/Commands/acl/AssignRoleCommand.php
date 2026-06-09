<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class AssignRoleCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'assign-role';
        $this->description = 'Asigna un rol a un usuario (INSERT en user_roles). No modifica config/acl.php.';
        $this->aliases     = ['add-role'];
        $this->examples    = [
            'php com acl assign-role --email=user@example.com --role=supervisor',
            'php com acl assign-role --email=user@example.com --role=supervisor --dry-run',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'role'],
            'optional' => [],
            'flags'    => ['dry-run'],
            'options'  => [
                'email'   => ['describe' => 'Email del usuario'],
                'role'    => ['describe' => 'Nombre del rol a asignar'],
                'dry-run' => ['describe' => 'Mostrar la acción sin ejecutarla'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email   = $this->opt($parsed, 'email');
        $roleName = $this->opt($parsed, 'role');
        $dryRun  = $this->opt($parsed, 'dry_run', false);

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $role = $this->getRoleByName($roleName);
        if (!$role) {
            echo "✗ Rol '{$roleName}' no existe en DB. Verifique 'php com acl list-roles'.\n";
            return;
        }

        $uid    = $user[$this->idField];
        $roleId = $role['id'];

        $exists = $this->withDb(fn() =>
            DB::table('user_roles')
                ->where(['user_id' => $uid, 'role_id' => $roleId])
                ->first()
        );

        if ($exists) {
            echo "⚠ El usuario ya tiene el rol '{$roleName}'.\n";
            return;
        }

        if ($dryRun) {
            $this->printDryRun("INSERT user_roles (user_id={$uid}, role_id={$roleId}) -- rol: {$roleName}");
            return;
        }

        $this->withDb(fn() =>
            DB::table('user_roles')->insert(['user_id' => $uid, 'role_id' => $roleId])
        );
        echo "✓ Rol '{$roleName}' asignado a {$email}.\n";
    }
}
