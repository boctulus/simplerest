<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseUsersCommand.php';

class ListUsersCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-users';
        $this->description = 'Lista todos los usuarios';
        $this->aliases     = ['ls', 'list', 'ls-users'];
        $this->examples    = [
            'php com users list-users',
            'php com users list-users --role=admin',
            'php com users list-users --disabled-only',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['role'],
            'flags'    => ['disabled-only'],
            'options'  => [
                'role'          => ['describe' => 'Filtrar por rol'],
                'disabled-only' => ['describe' => 'Mostrar solo usuarios deshabilitados'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $roleFilter   = $this->opt($parsed, 'role');
        $disabledOnly = $this->opt($parsed, 'disabled_only', false);

        $query = DB::table($this->usersTable)
            ->select([
                $this->usersTable . '.' . $this->idField,
                $this->usersTable . '.' . $this->emailField,
                'firstname', 'lastname',
                $this->isActiveField,
                $this->confirmedField,
            ]);

        if ($disabledOnly) {
            $query = $query->where($this->isActiveField, 0);
        }

        if ($roleFilter) {
            $query = $query
                ->join('user_roles', $this->usersTable . '.' . $this->idField, '=', 'user_roles.user_id')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('roles.name', $roleFilter);
        }

        $users = $query->get();

        if (empty($users)) {
            echo "No se encontraron usuarios.\n";
            return;
        }

        echo str_pad('', 65, '=') . "\n";
        foreach ($users as $user) {
            $active   = ($user[$this->isActiveField]  ?? 1) ? '✓' : '✗';
            $verified = ($user[$this->confirmedField]  ?? 0) ? '✓' : '✗';
            $name     = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
            $id       = $user[$this->idField];

            echo "  {$active} [{$id}] {$user[$this->emailField]}\n";
            if ($name) echo "     Nombre:     {$name}\n";
            echo "     Verificado: {$verified}\n\n";
        }
        echo count($users) . " usuario(s) encontrado(s).\n";
    }
}
