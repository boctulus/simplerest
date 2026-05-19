<?php

require_once __DIR__ . '/BaseAclCommand.php';

class ListDenyCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-deny';
        $this->description = 'Lista las deny rules de un usuario (user_deny_permissions). Precedencia: DENY > USER_GRANT > ROLE_GRANT.';
        $this->aliases     = ['ls-deny'];
        $this->examples    = [
            'php com acl list-deny user@example.com',
            'php com acl list-deny --email=user@example.com',
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
        $rows = $this->getUserDenyPerms($uid);

        echo "Deny rules de {$user[$this->emailField]}:\n";
        echo "  Precedencia: DENY > USER_GRANT > ROLE_GRANT\n";

        if (empty($rows)) {
            echo "  (sin deny rules)\n";
            return;
        }

        $this->printTable($rows, ['id', 'resource', 'action']);
    }
}
