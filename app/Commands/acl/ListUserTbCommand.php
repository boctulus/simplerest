<?php

require_once __DIR__ . '/BaseAclCommand.php';

class ListUserTbCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-user-tb';
        $this->description = 'Lista los permisos de tabla individuales de un usuario (user_tb_permissions)';
        $this->aliases     = ['ls-user-tb'];
        $this->examples    = [
            'php com acl list-user-tb user@example.com',
            'php com acl list-user-tb --email=user@example.com',
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

        $uid  = $user[$this->idField];
        $rows = $this->getUserTbPerms($uid);

        echo "Resource permissions (user_tb_permissions) de {$user[$this->emailField]}:\n";

        if (empty($rows)) {
            echo "  (sin permisos de tabla individuales)\n";
            return;
        }

        $actionFields = [
            'can_list_all' => 'list_all',
            'can_show_all' => 'show_all',
            'can_list'     => 'list',
            'can_show'     => 'show',
            'can_create'   => 'create',
            'can_update'   => 'update',
            'can_delete'   => 'delete',
        ];

        foreach ($rows as $row) {
            $active = [];
            foreach ($actionFields as $field => $label) {
                if (!empty($row[$field])) {
                    $active[] = $label;
                }
            }
            $permStr = empty($active) ? '(ninguno)' : implode(', ', $active);
            echo "  {$row['tb']}: {$permStr}\n";
        }
    }
}
