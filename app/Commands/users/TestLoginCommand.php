<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class TestLoginCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'test-login';
        $this->description = 'Verifica las credenciales de un usuario';
        $this->examples    = [
            'php com users test-login --email=user@example.com --password=secret123',
            'php com users test-login --uid=331 --password=secret123',
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
                'password' => ['describe' => 'Contraseña a verificar'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid   = $this->opt($parsed, 'uid');
        $email = $this->opt($parsed, 'email');
        $pass  = $this->opt($parsed, 'password');

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

        $hash = $user[$this->passwordField] ?? '';

        if (password_verify($pass, $hash)) {
            echo "✓ Login exitoso para {$label}\n";
            echo "  ID:     {$user[$this->idField]}\n";
            echo "  Rol:    " . ($this->getUserRole($user[$this->idField]) ?? 'N/A') . "\n";
            echo "  Activo: " . (($user[$this->isActiveField] ?? 1) ? '✓' : '✗') . "\n";
        } else {
            echo "✗ Credenciales inválidas para {$label}.\n";
        }
    }
}
