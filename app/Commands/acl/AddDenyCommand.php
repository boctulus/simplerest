<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class AddDenyCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'add-deny';
        $this->description = 'Agrega una deny rule a un usuario (INSERT user_deny_permissions). DENY tiene precedencia sobre cualquier ALLOW.';
        $this->aliases     = ['deny'];
        $this->examples    = [
            'php com acl add-deny --email=user@example.com --resource=products --action=delete',
            'php com acl add-deny --email=user@example.com --resource=products --action=delete --dry-run',
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
                'action'   => ['describe' => 'Acción a denegar: show|list|create|update|delete|show_all|list_all'],
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

        $validActions = ['show', 'list', 'create', 'update', 'delete', 'show_all', 'list_all'];
        if (!in_array($action, $validActions)) {
            echo "✗ Acción inválida: '{$action}'. Válidas: " . implode(', ', $validActions) . "\n";
            return;
        }

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

        if ($exists) {
            echo "⚠ Ya existe deny rule: {$resource}.{$action} para {$email}.\n";
            return;
        }

        if ($dryRun) {
            $this->printDryRun("INSERT user_deny_permissions (user_id={$uid}, resource={$resource}, action={$action})");
            return;
        }

        $this->withDb(fn() =>
            DB::table('user_deny_permissions')->insert([
                'user_id'    => $uid,
                'resource'   => $resource,
                'action'     => $action,
                'created_by' => $uid,
                'created_at' => date('Y-m-d H:i:s'),
            ])
        );

        echo "✓ Deny rule '{$resource}.{$action}' agregada para {$email}.\n";
    }
}
