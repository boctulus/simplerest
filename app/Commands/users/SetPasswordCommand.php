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
            'php com users set-password --uid=331 --password=NuevaClave123',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['password'],
            'optional' => ['email', 'uid'],
            'flags'    => [],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario (alternativa a --uid)'],
                'uid'      => ['describe' => 'ID del usuario (alternativa a --email)'],
                'password' => ['describe' => 'Nueva contraseña'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid     = $this->opt($parsed, 'uid');
        $email   = $this->opt($parsed, 'email');
        $newPass = $this->opt($parsed, 'password');

        if (!$uid && !$email) {
            echo "✗ Debes proporcionar --email o --uid.\n";
            return;
        }

        if ($uid) {
            $user = $this->getUserById((int) $uid);
            if (!$user) { echo "✗ Usuario con ID '{$uid}' no encontrado.\n"; return; }
            $label = "ID {$uid}";
        } else {
            $user = $this->getUserByEmail($email);
            if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }
            $label = "'{$email}'";
        }

        $this->updateUser($user[$this->idField], [
            $this->passwordField => password_hash($newPass, PASSWORD_DEFAULT),
        ]);
        echo "✓ Contraseña actualizada para {$label}.\n";
    }
}
