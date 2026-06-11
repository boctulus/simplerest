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
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email', 'uid'],
            'flags'    => [],
            'options'  => [
                'email' => ['describe' => 'Email (o primer arg posicional)'],
                'uid'   => ['describe' => 'ID del usuario (alternativa a --email)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid   = $this->opt($parsed, 'uid');
        $email = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);

        if (!$uid && !$email) { echo "✗ Debes proporcionar --email o --uid.\n"; return; }

        if ($uid) {
            $user  = $this->getUserById((int) $uid);
            $label = "ID {$uid}";
            if (!$user) { echo "✗ Usuario con ID '{$uid}' no encontrado.\n"; return; }
        } else {
            $user  = $this->getUserByEmail($email);
            $label = "'{$email}'";
            if (!$user) { echo "✗ Usuario '{$email}' no encontrado.\n"; return; }
        }

        $this->updateUser($user[$this->idField], [$this->isActiveField => 1]);
        echo "✓ Usuario {$label} habilitado.\n";
    }
}
