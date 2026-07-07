 q<?php

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

    private function tb(array $headers, array $rows): void
    {
        $colW = array_map(fn($h) => mb_strlen($h, 'UTF-8') + 2, $headers);

        foreach ($rows as $row) {
            foreach ($row as $i => $val) {
                $len = mb_strlen(preg_replace('/\033\[[0-9;]*m/', '', $val), 'UTF-8');
                if ($len + 2 > $colW[$i]) {
                    $colW[$i] = $len + 2;
                }
            }
        }

        $hor = '─';
        echo "\033[90m┌" . implode('┬', array_map(fn($w) => str_repeat($hor, $w), $colW)) . "┐\033[0m\n";
        echo '│';
        foreach ($headers as $i => $h) {
            echo "\033[1;37m" . str_pad($h, $colW[$i], ' ', STR_PAD_BOTH) . "\033[0m│";
        }
        echo "\n";
        echo "\033[90m├" . implode('┼', array_map(fn($w) => str_repeat($hor, $w), $colW)) . "┤\033[0m\n";

        foreach ($rows as $row) {
            echo '│';
            foreach ($row as $i => $val) {
                $plain = preg_replace('/\033\[[0-9;]*m/', '', $val);
                $pad = $colW[$i] - mb_strlen($plain, 'UTF-8');
                echo $val . str_repeat(' ', $pad) . '│';
            }
            echo "\n";
        }

        echo "\033[90m└" . implode('┴', array_map(fn($w) => str_repeat($hor, $w), $colW)) . "┘\033[0m\n";
    }

    public function execute(array $parsed): void
    {
        $roleFilter   = $this->opt($parsed, 'role');
        $disabledOnly = $this->opt($parsed, 'disabled_only', false);

        $query = DB::table($this->usersTable)
            ->select([
                $this->usersTable . '.' . $this->idField,
                $this->usersTable . '.' . $this->emailField,
                $this->usersTable . '.name',
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
            echo "\033[33m⚠ No se encontraron usuarios.\033[0m\n";
            return;
        }

        $rows = [];
        foreach ($users as $u) {
            $active   = ($u[$this->isActiveField] ?? 1);
            $verified = ($u[$this->confirmedField] ?? 0);
            $email    = $u[$this->emailField] ?? '';

            $rows[] = [
                ($active ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m"),
                "\033[36m" . $u[$this->idField] . "\033[0m",
                $email ? "\033[33m{$email}\033[0m" : "\033[90m-\033[0m",
                ($u['name'] ?? '') ? "\033[1m{$u['name']}\033[0m" : "\033[90m-\033[0m",
                ($verified ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m"),
            ];
        }

        $this->tb(
            ['', 'ID', 'Email', 'Nombre', 'Verif.'],
            $rows
        );

        echo "\033[36m→ " . count($users) . " usuario(s) encontrado(s).\033[0m\n";
    }
}
