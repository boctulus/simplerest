<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class RemoveDenyCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'remove-deny';
        $this->description = 'Elimina una deny rule de un usuario (DELETE user_deny_permissions)';
        $this->aliases     = ['rm-deny'];
        $this->examples    = [
            'php com acl remove-deny --email=user@example.com --resource=products --action=delete',
            'php com acl remove-deny --email=user@example.com --resource=products --action=delete --dry-run',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'resource', 'action'],
            'optional' => [],
            'flags'    => ['dry-run'],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario'],
                'resource' => ['describe' => 'Nombre del recurso/tabla'],
                'action'   => ['describe' => 'Acción: show|list|create|update|delete|show_all|list_all'],
                'dry-run'  => ['describe' => 'Mostrar la acción sin ejecutarla'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email    = $this->opt($parsed, 'email');
        $resource = $this->opt($parsed, 'resource');
        $action   = $this->opt($parsed, 'action');
        $dryRun   = $this->opt($parsed, 'dry_run', false);

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $uid = $user[$this->idField];

        $exists = $this->withDb(fn() =>
            DB::table('user_deny_permissions')
                ->where(['user_id' => $uid, 'resource' => $resource, 'action' => $action])
                ->first()
        );

        if (!$exists) {
            echo "⚠ No existe deny rule: {$resource}.{$action} para {$email}.\n";
            return;
        }

        if ($dryRun) {
            $this->printDryRun("DELETE user_deny_permissions WHERE user_id={$uid} AND resource={$resource} AND action={$action}");
            return;
        }

        $this->withDb(fn() =>
            DB::table('user_deny_permissions')
                ->where(['user_id' => $uid, 'resource' => $resource, 'action' => $action])
                ->delete()
        );

        echo "✓ Deny rule '{$resource}.{$action}' eliminada para {$email}.\n";
    }
}
