<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class ValidateCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'validate';
        $this->description = 'Verifica consistencia del ACL: roles huérfanos, permisos inválidos, herencia rota, deny rules inválidas';
        $this->aliases     = [];
        $this->examples    = [
            'php com acl validate',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => [],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $issues = [];

        echo "Validando ACL...\n\n";

        // Load ACL policy
        try {
            $acl      = $this->loadAcl();
            $snapshot = $acl->getSnapshot();
        } catch (\Exception $e) {
            echo "✗ Error cargando ACL: " . $e->getMessage() . "\n";
            return;
        }

        $policyRoles  = array_keys($snapshot->rolePerms);
        $validSpPerms = $snapshot->validSpPerms;

        // --- 1. Roles in DB vs policy ---
        $dbRoles     = $this->withDb(fn() => DB::table('roles')->get());
        $dbRoleNames = array_column($dbRoles, 'name');

        foreach ($dbRoleNames as $dbRole) {
            if (!in_array($dbRole, $policyRoles)) {
                $issues[] = "⚠ Rol '{$dbRole}' está en DB (roles) pero NO en config/acl.php. Ejecute 'php com acl make --force'.";
            }
        }
        foreach ($policyRoles as $pRole) {
            if (!in_array($pRole, $dbRoleNames)) {
                $issues[] = "⚠ Rol '{$pRole}' está en config/acl.php pero NO en DB. Ejecute 'php com acl make'.";
            }
        }

        // --- 2. user_roles → roles (orphaned role assignments) ---
        $orphanedRoles = $this->withDb(fn() =>
            DB::table('user_roles')
                ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
                ->whereNull('roles.id')
                ->get()
        );

        foreach ($orphanedRoles as $row) {
            $issues[] = "✗ user_roles: user_id={$row['user_id']} → role_id={$row['role_id']} no existe en roles (huérfano).";
        }

        // --- 3. user_sp_permissions → sp_permissions ---
        $orphanedSp = $this->withDb(fn() =>
            DB::table('user_sp_permissions')
                ->leftJoin('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
                ->whereNull('sp_permissions.id')
                ->get()
        );

        foreach ($orphanedSp as $row) {
            $issues[] = "✗ user_sp_permissions: user_id={$row['user_id']} → sp_permission_id={$row['sp_permission_id']} no existe (huérfano).";
        }

        // --- 4. sp_permissions names vs ACL valid sp ---
        $dbSpNames = $this->withDb(fn() => DB::table('sp_permissions')->pluck('name'));
        foreach ($dbSpNames as $name) {
            if (!in_array($name, $validSpPerms)) {
                $issues[] = "⚠ sp_permissions: '{$name}' está en DB pero NO en ACL válidos. Puede ser un permiso custom.";
            }
        }

        // --- 5. user_deny_permissions: valid actions ---
        $validActions = ['show', 'list', 'create', 'update', 'delete', 'show_all', 'list_all'];
        $denyRows     = $this->withDb(fn() => DB::table('user_deny_permissions')->get());
        foreach ($denyRows as $row) {
            if (!in_array($row['action'], $validActions)) {
                $issues[] = "✗ user_deny_permissions: id={$row['id']} tiene acción inválida '{$row['action']}'.";
            }
        }

        // --- 6. Inheritance chain (detect broken ancestors) ---
        foreach ($policyRoles as $role) {
            try {
                $ancestry = $acl->getAncestry($role);
                // If a role in ancestry doesn't exist in policy, something is wrong
                foreach ($ancestry as $ancestor) {
                    if (!in_array($ancestor, $policyRoles)) {
                        $issues[] = "✗ Herencia rota: rol '{$role}' hereda de '{$ancestor}' que no existe en la política.";
                    }
                }
            } catch (\Exception $e) {
                $issues[] = "✗ Error en herencia de rol '{$role}': " . $e->getMessage();
            }
        }

        // --- Report ---
        if (empty($issues)) {
            echo "✓ ACL consistente. No se encontraron problemas.\n";
        } else {
            echo count($issues) . " problema(s) encontrado(s):\n\n";
            foreach ($issues as $issue) {
                echo "  {$issue}\n";
            }
        }
    }
}
