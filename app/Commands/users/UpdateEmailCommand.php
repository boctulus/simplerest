<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseUsersCommand.php';

class UpdateEmailCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'update-email';
        $this->description = 'Actualiza el email de un usuario';
        $this->examples    = [
            'php com users update-email --uid=5 --email=nuevo@example.com',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['uid', 'email'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'uid'   => ['describe' => 'ID del usuario'],
                'email' => ['describe' => 'Nuevo email'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid      = $this->opt($parsed, 'uid');
        $newEmail = $this->opt($parsed, 'email');

        $user = DB::table($this->usersTable)->where([$this->idField => $uid])->first();
        if (!$user) { echo "✗ Usuario con ID '{$uid}' no encontrado.\n"; return; }

        if ($this->getUserByEmail($newEmail)) {
            echo "✗ El email '{$newEmail}' ya está en uso.\n";
            return;
        }

        $oldEmail = $user[$this->emailField];
        $this->updateUser((int) $uid, [
            $this->emailField    => $newEmail,
            $this->confirmedField => 0,
        ]);
        echo "✓ Email actualizado de '{$oldEmail}' a '{$newEmail}'.\n";
    }
}
