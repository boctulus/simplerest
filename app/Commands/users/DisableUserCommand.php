<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class DisableUserCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'disable-user';
        $this->description = 'Deshabilita una cuenta de usuario';
        $this->aliases     = ['disable'];
        $this->examples    = ['php com users disable-user user@example.com'];
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

        $this->updateUser($user[$this->idField], [$this->isActiveField => 0]);
        echo "✓ Usuario '{$email}' deshabilitado.\n";
    }
}
