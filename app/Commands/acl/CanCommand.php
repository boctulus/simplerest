<?php

require_once __DIR__ . '/BaseAclCommand.php';

class CanCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'can';
        $this->description = '¿Tiene el usuario ese permiso? (boolean). Para el chain completo use: php com acl explain';
        $this->aliases     = ['check'];
        $this->examples    = [
            'php com acl can --email=user@example.com --perm=delete --resource=products',
            'php com acl can --email=user@example.com --perm=impersonate',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'perm'],
            'optional' => ['resource'],
            'flags'    => [],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario'],
                'perm'     => ['describe' => 'Permiso a verificar (show|list|create|update|delete|show_all|list_all o sp_permission name)'],
                'resource' => ['describe' => 'Recurso/tabla (requerido para resource permissions, omitir para sp)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        if (!$this->validate($parsed)) return;

        $email    = $this->opt($parsed, 'email');
        $perm     = $this->opt($parsed, 'perm');
        $resource = $this->opt($parsed, 'resource');

        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return;
        }

        $uid = $user[$this->idField];
        $ctx = $this->buildUserAclContext($uid);

        /** @var \Boctulus\FineGrainedACL\Acl $acl */
        $acl     = $ctx['acl'];
        $context = $ctx['context'];
        $engine  = $acl->getEngine();

        $spPerms = ['read_all','read_all_folders','read_all_trashcan','write_all','write_all_folders',
                    'write_all_trashcan','write_all_collections','fill_all','grant','impersonate','lock','transfer'];

        if (in_array($perm, $spPerms)) {
            $result = $engine->hasSpecialPermission($perm, $context);
            $label  = $result ? 'ALLOW' : 'DENY';
            $icon   = $result ? '✓' : '✗';
            echo "{$icon} {$email} | sp:{$perm} → {$label}\n";
        } elseif ($resource) {
            $result = $engine->can($context, $perm, $resource);
            $label  = $result ? 'ALLOW' : 'DENY';
            $icon   = $result ? '✓' : '✗';
            echo "{$icon} {$email} | {$resource}.{$perm} → {$label}\n";
        } else {
            echo "✗ Para resource permissions se requiere --resource=<tabla>.\n";
            echo "  Para special permissions omita --resource= y use el nombre sp (ej: impersonate).\n";
        }
    }
}
