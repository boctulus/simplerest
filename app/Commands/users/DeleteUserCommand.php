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
            'php com users delete-user --uid=331 --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email', 'uid'],
            'flags'    => ['force'],
            'options'  => [
                'email' => ['describe' => 'Email del usuario (o primer arg posicional)'],
                'uid'   => ['describe' => 'ID del usuario (alternativa a --email)'],
                'force' => ['describe' => 'Confirmar la eliminación'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $uid   = $this->opt($parsed, 'uid');
        $email = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);
        $force = $this->opt($parsed, 'force', false);

        if (!$uid && !$email) {
            echo "✗ Debes proporcionar --email o --uid.\n";
            $this->showUsage();
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

        if (!$force) {
            echo "¿Estás seguro? Agrega --force para confirmar la eliminación de {$label}.\n";
            return;
        }

        $this->updateUser($user[$this->idField], ['deleted_at' => date('Y-m-d H:i:s')]);
        echo "✓ Usuario {$label} eliminado (soft delete).\n";
    }
}
