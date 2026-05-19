<?php

use Boctulus\Simplerest\Core\Security\Domain\CapabilityTypeResolver;

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
            'php com acl can --email=user@example.com --perm=cashbox.open',
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
                'perm'     => ['describe' => 'Acción CRUD (con --resource), SP del sistema (impersonate) o domain capability (cashbox.open)'],
                'resource' => ['describe' => 'Recurso/tabla (requerido para CRUD, omitir para SPs y domain capabilities)'],
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
        $acl      = $ctx['acl'];
        $context  = $ctx['context'];
        $engine   = $acl->getEngine();
        $snapshot = $acl->getSnapshot();

        $dbSpPerms = $this->dbSpPerms();
        $resolved  = CapabilityTypeResolver::resolve($perm, $resource, $snapshot, $dbSpPerms);

        switch ($resolved['type']) {

            case CapabilityTypeResolver::RESOURCE:
                $known = CapabilityTypeResolver::knownResources($snapshot);
                if (!in_array($resolved['resource'], $known, true)) {
                    $suggestion = CapabilityTypeResolver::suggest($resolved['resource'], $known);
                    echo "✗ Recurso desconocido: '{$resolved['resource']}'\n";
                    if ($suggestion) {
                        echo "  ¿Quisiste decir: {$suggestion}?\n";
                    } elseif (!empty($known)) {
                        echo "  Recursos conocidos: " . implode(', ', $known) . "\n";
                    }
                    return;
                }
                $result = $engine->can($context, $resolved['action'], $resolved['resource']);
                $label  = $result ? 'ALLOW' : 'DENY';
                $icon   = $result ? '✓' : '✗';
                echo "{$icon} {$email} | {$resolved['capability']} → {$label}\n";
                break;

            case CapabilityTypeResolver::SYSTEM_SP:
                $result = $engine->hasSpecialPermission($resolved['capability'], $context);
                $label  = $result ? 'ALLOW' : 'DENY';
                $icon   = $result ? '✓' : '✗';
                echo "{$icon} {$email} | sp:{$resolved['capability']} → {$label}\n";
                break;

            case CapabilityTypeResolver::DOMAIN_SP:
                if (!in_array($resolved['capability'], $dbSpPerms, true)) {
                    $suggestion = CapabilityTypeResolver::suggest($resolved['capability'], $dbSpPerms);
                    echo "✗ Domain capability desconocida: '{$resolved['capability']}'\n";
                    if ($suggestion) {
                        echo "  ¿Quisiste decir: {$suggestion}?\n";
                    }
                    return;
                }
                $result = $engine->hasSpecialPermission($resolved['capability'], $context);
                $label  = $result ? 'ALLOW' : 'DENY';
                $icon   = $result ? '✓' : '✗';
                echo "{$icon} {$email} | domain:{$resolved['capability']} → {$label}\n";
                break;

            default:
                $allCaps = array_merge($snapshot->validSpPerms, $dbSpPerms);
                $suggestion = CapabilityTypeResolver::suggest($perm, $allCaps);
                echo "✗ Capability inválida: '{$perm}'\n";
                if ($suggestion) {
                    echo "  ¿Quisiste decir: {$suggestion}?\n";
                }
                echo "  Para resource permissions use: --perm=<action> --resource=<tabla>\n";
                break;
        }
    }
}
