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
                'password' => ['describe' => 'Contraseña a verificar'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $email = $this->opt($parsed, 'email');
        $pass  = $this->opt($parsed, 'password');

        $user = $this->getUserByEmail($email);
        if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }

        $hash = $user[$this->passwordField] ?? '';

        if (password_verify($pass, $hash)) {
            echo "✓ Login exitoso para '{$email}'\n";
            echo "  ID:     {$user[$this->idField]}\n";
            echo "  Rol:    " . ($this->getUserRole($user[$this->idField]) ?? 'N/A') . "\n";
            echo "  Activo: " . (($user[$this->isActiveField] ?? 1) ? '✓' : '✗') . "\n";
        } else {
            echo "✗ Credenciales inválidas para '{$email}'.\n";
        }
    }
}
