<?php

require_once __DIR__ . '/BaseAclCommand.php';

class ListUserSpCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-user-sp';
        $this->description = 'Lista los special permissions asignados individualmente a un usuario';
        $this->aliases     = ['ls-user-sp'];
        $this->examples    = [
            'php com acl list-user-sp user@example.com',
            'php com acl list-user-sp --email=user@example.com',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['email'],
            'flags'    => [],
            'options'  => [
                'email' => ['describe' => 'Email del usuario (o primer arg posicional)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $user = $this->requireUser($parsed);
        if (!$user) return;

        $uid   = $user[$this->idField];
        $perms = $this->getUserSpPerms($uid);

        echo "Special permissions de {$user[$this->emailField]}:\n";

        if (empty($perms)) {
            echo "  (sin special permissions individuales)\n";
            return;
        }

        foreach ($perms as $p) {
            echo "  • [{$p['sp_permission_id']}] {$p['name']}\n";
        }
    }
}
