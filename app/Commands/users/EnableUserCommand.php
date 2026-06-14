<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class EnableUserCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'enable-user';
        $this->description = 'Habilita una cuenta de usuario';
        $this->aliases     = ['enable'];
        $this->examples    = [
            'php com users enable-user user@example.com',
            'php com users enable-user --uid=331',
            'php com users enable-user --username=boctulus',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email', 'uid', 'username'],
            'flags'    => [],
            'options'  => [
                'email'    => ['describe' => 'Email (o primer arg posicional)'],
                'uid'      => ['describe' => 'ID del usuario (alternativa a --email o --username)'],
                'username' => ['describe' => 'Username del usuario (alternativa a --email o --uid)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid      = $this->opt($parsed, 'uid');
        $email    = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);
        $username = $this->opt($parsed, 'username');

        if (!$uid && !$email && !$username) { echo "✗ Debes proporcionar --email, --uid o --username.\n"; return; }

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

        $this->updateUser($user[$this->idField], [$this->isActiveField => 1]);
        echo "✓ Usuario {$label} habilitado.\n";
    }
}
