<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class DeleteUserCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'delete-user';
        $this->description = 'Deshabilita un usuario (is_active = 0)';
        $this->aliases     = ['delete', 'del', 'rm', 'remove'];
        $this->examples    = [
            'php com users delete-user user@example.com --force',
            'php com users delete-user --email=user@example.com --force',
            'php com users delete-user --uid=331 --force',
            'php com users delete-user --username=boctulus --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email', 'uid', 'username'],
            'flags'    => ['force'],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario (o primer arg posicional)'],
                'uid'      => ['describe' => 'ID del usuario (alternativa a --email o --username)'],
                'username' => ['describe' => 'Username del usuario (alternativa a --email o --uid)'],
                'force'    => ['describe' => 'Confirmar la eliminación'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid      = $this->opt($parsed, 'uid');
        $email    = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);
        $username = $this->opt($parsed, 'username');
        $force    = $this->opt($parsed, 'force', false);

        if (!$uid && !$email && !$username) {
            echo "✗ Debes proporcionar --email, --uid o --username.\n";
            $this->showUsage();
            return;
        }

        if ($uid) {
            $user  = $this->getUserById((int) $uid);
            $label = "ID {$uid}";
            if (!$user) { echo "✗ Usuario con ID '{$uid}' no encontrado.\n"; return; }
        } elseif ($username) {
            $user  = $this->getUserByUsername($username);
            $label = "'{$username}'";
            if (!$user) { echo "✗ Usuario '{$username}' no encontrado.\n"; return; }
        } else {
            $user  = $this->getUserByEmail($email);
            $label = "'{$email}'";
            if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }
        }

        if (!$force) {
            echo "¿Estás seguro? Agrega --force para confirmar la eliminación de {$label}.\n";
            return;
        }

        $this->updateUser($user[$this->idField], [$this->isActiveField => 0]);
        echo "✓ Usuario {$label} deshabilitado.\n";
    }
}
