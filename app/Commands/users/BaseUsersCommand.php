<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;

abstract class BaseUsersCommand extends BaseCommand
{
    protected string $usersTable;
    protected string $idField;
    protected string $emailField      = 'email';
    protected string $usernameField   = 'username';
    protected string $passwordField   = 'password';
    protected string $confirmedField  = 'confirmed_email';
    protected string $isActiveField   = 'is_active';

    public function __construct()
    {
        $this->usersTable = get_users_table();
        $this->idField    = get_id_name($this->usersTable);
    }

    protected function getUserByEmail(string $email): ?array
    {
        return DB::table($this->usersTable)
            ->unhide(['password'])
            ->where([$this->emailField => $email])
            ->first() ?: null;
    }

    protected function updateUser(int $id, array $data): void
    {
        $parts = [];
        $vals  = [];
        foreach ($data as $col => $val) {
            $parts[] = "`{$col}` = ?";
            $vals[]  = $val;
        }
        $vals[] = $id;
        DB::statement(
            "UPDATE `{$this->usersTable}` SET " . implode(', ', $parts) . " WHERE `{$this->idField}` = ?",
            $vals
        );
    }

    protected function assignRole(int $userId, string $roleName): bool
    {
        $role = DB::table('roles')->where(['name' => $roleName])->first();
        if (!$role) {
            echo "  ⚠ Rol '{$roleName}' no encontrado.\n";
            return false;
        }
        DB::table('user_roles')->insert(['user_id' => $userId, 'role_id' => $role['id']]);
        return true;
    }

    protected function getUserRole(int $userId): ?string
    {
        return DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('user_roles.user_id', $userId)
            ->value('roles.name') ?: null;
    }
}
