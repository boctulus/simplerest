<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class SetPasswordCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'set-password';
        $this->description = 'Establece una nueva contraseña para un usuario';
        $this->examples    = [
            'php com users set-password --email=user@example.com --password=newPass123',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'password'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario'],
                'password' => ['describe' => 'Nueva contraseña'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $email   = $this->opt($parsed, 'email');
        $newPass = $this->opt($parsed, 'password');

        $user = $this->getUserByEmail($email);
        if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }

        $this->updateUser($user[$this->idField], [
            $this->passwordField => password_hash($newPass, PASSWORD_DEFAULT),
        ]);
        echo "✓ Contraseña actualizada para '{$email}'.\n";
    }
}
