<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseUsersCommand.php';

class SetRoleCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'set-role';
        $this->description = 'Asigna un rol a un usuario';
        $this->examples    = [
            'php com users set-role --email=user@example.com --role=admin',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'role'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'email' => ['describe' => 'Email del usuario'],
                'role'  => ['describe' => 'Nombre del rol'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $email  = $this->opt($parsed, 'email');
        $role   = $this->opt($parsed, 'role');

        $user = $this->getUserByEmail($email);
        if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }

        $userId  = $user[$this->idField];
        $roleRow = DB::table('roles')->where(['name' => $role])->first();
        if (!$roleRow) { echo "✗ Rol '{$role}' no existe.\n"; return; }

        $existing = DB::table('user_roles')->where(['user_id' => $userId])->first();
        if ($existing) {
            DB::table('user_roles')->where(['user_id' => $userId])->update(['role_id' => $roleRow['id']]);
        } else {
            DB::table('user_roles')->insert(['user_id' => $userId, 'role_id' => $roleRow['id']]);
        }

        echo "✓ Rol '{$role}' asignado a '{$email}'.\n";
    }
}
