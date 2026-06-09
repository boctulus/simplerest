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
        $this->examples    = ['php com users enable-user user@example.com'];
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

        $this->updateUser($user[$this->idField], [$this->isActiveField => 1]);
        echo "✓ Usuario '{$email}' habilitado.\n";
    }
}
