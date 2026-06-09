<?php

require_once __DIR__ . '/BaseAclCommand.php';

class ShowUserRolesCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'show-user-roles';
        $this->description = 'Muestra los roles asignados a un usuario específico';
        $this->aliases     = ['show-ur'];
        $this->examples    = [
            'php com acl show-user-roles user@example.com',
            'php com acl show-user-roles --email=user@example.com',
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
        $roles = $this->getUserRoles($uid);

        echo "Roles de {$user[$this->emailField]}:\n";

        if (empty($roles)) {
            echo "  (sin roles asignados)\n";
            return;
        }

        foreach ($roles as $r) {
            echo "  • [{$r['role_id']}] {$r['name']}\n";
        }
    }
}
