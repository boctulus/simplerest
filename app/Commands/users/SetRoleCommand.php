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
            'php com users set-role --uid=331 --role=admin',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['role'],
            'optional' => ['email', 'uid'],
            'flags'    => [],
            'options'  => [
                'email' => ['describe' => 'Email del usuario (alternativa a --uid)'],
                'uid'   => ['describe' => 'ID del usuario (alternativa a --email)'],
                'role'  => ['describe' => 'Nombre del rol'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid   = $this->opt($parsed, 'uid');
        $email = $this->opt($parsed, 'email');
        $role  = $this->opt($parsed, 'role');

        if (!$uid && !$email) {
            echo "✗ Debes proporcionar --email o --uid.\n";
            return;
        }

        if ($uid) {
            $user  = $this->getUserById((int) $uid);
            $label = "ID {$uid}";
            if (!$user) { echo "✗ Usuario con ID '{$uid}' no encontrado.\n"; return; }
        } else {
            $user  = $this->getUserByEmail($email);
            $label = "'{$email}'";
            if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }
        }

        $userId  = $user[$this->idField];
        $roleRow = DB::table('roles')->where(['name' => $role])->first();
        if (!$roleRow) { echo "✗ Rol '{$role}' no existe.\n"; return; }

        $existing = DB::table('user_roles')->where(['user_id' => $userId])->first();
        if ($existing) {
            DB::table('user_roles')->where(['user_id' => $userId])->update(['role_id' => $roleRow['id']]);
        } else {
            DB::table('user_roles')->insert(['user_id' => $userId, 'role_id' => $roleRow['id']]);
        }

        echo "✓ Rol '{$role}' asignado a {$label}.\n";
    }
}
