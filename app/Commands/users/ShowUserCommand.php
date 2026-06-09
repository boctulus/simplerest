<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class ShowUserCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'show-user';
        $this->description = 'Muestra información completa de un usuario';
        $this->aliases     = ['show', 'get', 'info'];
        $this->examples    = [
            'php com users show-user user@example.com',
            'php com users show-user --email=user@example.com',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email'],
            'flags'    => [],
            'options'  => ['email' => ['describe' => 'Email (o primer arg posicional)']],
        ];
    }

    public function execute(array $parsed): void
    {
        $email = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);
        if (!$email) { echo "✗ Debes proporcionar un email.\n"; return; }

        $user = $this->getUserByEmail($email);
        if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }

        $id       = $user[$this->idField];
        $active   = ($user[$this->isActiveField]  ?? 1) ? '✓' : '✗';
        $verified = ($user[$this->confirmedField]  ?? 0) ? '✓' : '✗';
        $role     = $this->getUserRole($id);

        echo "Información del usuario:\n" . str_pad('', 40, '-') . "\n";
        echo "  ID:               {$id}\n";
        echo "  Email:            {$user[$this->emailField]}\n";
        echo "  Username:         " . ($user[$this->usernameField] ?? 'N/A') . "\n";
        echo "  Nombre:           " . trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) . "\n";
        echo "  Activo:           {$active}\n";
        echo "  Email verificado: {$verified}\n";
        echo "  Rol:              " . ($role ?? 'N/A') . "\n";
        if (!empty($user['created_at'])) echo "  Creado:           {$user['created_at']}\n";
        if (!empty($user['updated_at'])) echo "  Actualizado:      {$user['updated_at']}\n";
    }
}
