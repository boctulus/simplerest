<?php

require_once __DIR__ . '/BaseUsersCommand.php';

class DeleteUserCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'delete-user';
        $this->description = 'Elimina un usuario (soft delete)';
        $this->aliases     = ['delete', 'del', 'rm', 'remove'];
        $this->examples    = [
            'php com users delete-user user@example.com --force',
            'php com users delete-user --email=user@example.com --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email'],
            'flags'    => ['force'],
            'options'  => [
                'email' => ['describe' => 'Email del usuario (o primer arg posicional)'],
                'force' => ['describe' => 'Confirmar la eliminación'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $email = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);
        $force = $this->opt($parsed, 'force', false);

        if (!$email) {
            echo "✗ Debes proporcionar un email.\n";
            $this->showUsage();
            return;
        }

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario '{$email}' no encontrado.\n";
            return;
        }

        if (!$force) {
            echo "¿Estás seguro? Agrega --force para confirmar la eliminación de '{$email}'.\n";
            return;
        }

        $this->updateUser($user[$this->idField], ['deleted_at' => date('Y-m-d H:i:s')]);
        echo "✓ Usuario '{$email}' eliminado (soft delete).\n";
    }
}
