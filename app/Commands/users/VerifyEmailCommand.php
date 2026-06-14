<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class VerifyEmailCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'verify-email';
        $this->description = 'Marca el email de un usuario como verificado';
        $this->aliases     = ['verify'];
        $this->examples    = [
            'php com users verify-email user@example.com',
            'php com users verify-email --uid=331',
            'php com users verify-email --username=boctulus',
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

        $this->updateUser($user[$this->idField], [$this->confirmedField => 1]);
        echo "✓ Email {$label} marcado como verificado.\n";
    }
}
