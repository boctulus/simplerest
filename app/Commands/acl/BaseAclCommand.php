<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Security\Compiler\EffectivePermissionCompiler;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Domain\CapabilityTypeResolver;

abstract class BaseAclCommand extends BaseCommand
{
    protected string $usersTable;
    protected string $idField;
    protected string $emailField = 'email';

    public function __construct()
    {
        $this->usersTable = get_users_table();
        $this->idField    = get_id_name($this->usersTable);
    }

    // --- User resolution ---

    protected function getUserByEmail(string $email): ?array
    {
        return withDefaultConnection(fn() =>
            DB::table($this->usersTable)
                ->where([$this->emailField => $email])
                ->first() ?: null
        );
    }

    protected function resolveEmail(array $parsed): ?string
    {
        return $parsed['email'] ?? ($parsed['_positional'][0] ?? null);
    }

    protected function requireUser(array $parsed): ?array
    {
        $email = $this->resolveEmail($parsed);
        if (!$email) {
            echo "✗ Se requiere --email= o email como argumento posicional.\n";
            return null;
        }
        $user = $this->getUserByEmail($email);
        if (!$user) {
            echo "✗ Usuario no encontrado: {$email}\n";
            return null;
        }
        return $user;
    }

    // --- DB helpers (all wrapped in withDefaultConnection) ---

    protected function getRoleByName(string $name): ?array
    {
        return withDefaultConnection(fn() =>
            DB::table('roles')->where(['name' => $name])->first() ?: null
        );
    }

    protected function getSpPermByName(string $name): ?array
    {
        return withDefaultConnection(fn() =>
            DB::table('sp_permissions')->where(['name' => $name])->first() ?: null
        );
    }

    protected function getUserRoles(int $uid): array
    {
        return withDefaultConnection(fn() =>
            DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where(['user_roles.user_id' => $uid])
                ->get() ?: []
        );
    }

    protected function getUserSpPerms(int $uid): array
    {
        return withDefaultConnection(fn() =>
            DB::table('user_sp_permissions')
                ->join('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
                ->where(['user_sp_permissions.user_id' => $uid])
                ->get() ?: []
        );
    }

    protected function getUserTbPerms(int $uid): array
    {
        return withDefaultConnection(fn() =>
            DB::table('user_tb_permissions')
                ->where(['user_id' => $uid])
                ->get() ?: []
        );
    }

    protected function getUserDenyPerms(int $uid): array
    {
        return withDefaultConnection(fn() =>
            DB::table('user_deny_permissions')
                ->where(['user_id' => $uid])
                ->get() ?: []
        );
    }

    // --- ACL context builder for policy evaluation ---

    protected function buildUserAclContext(int $uid)
    {
        $acl = $this->loadAcl();

        $roles = withDefaultConnection(fn() =>
            DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where(['user_roles.user_id' => $uid])
                ->pluck('roles.name') ?: []
        );

        $userSpPerms = withDefaultConnection(fn() =>
            DB::table('user_sp_permissions')
                ->join('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
                ->where(['user_sp_permissions.user_id' => $uid])
                ->pluck('sp_permissions.name') ?: []
        );

        $tbRows      = $this->getUserTbPerms($uid);
        $userTbPerms = [];
        foreach ($tbRows as $row) {
            $packed = 0;
            if ($row['can_list_all']) $packed |= 64;
            if ($row['can_show_all']) $packed |= 32;
            if ($row['can_list'])     $packed |= 16;
            if ($row['can_show'])     $packed |= 8;
            if ($row['can_create'])   $packed |= 4;
            if ($row['can_update'])   $packed |= 2;
            if ($row['can_delete'])   $packed |= 1;
            $userTbPerms[$row['tb']] = $packed;
        }

        $denyRows      = $this->getUserDenyPerms($uid);
        $userDenyPerms = [];
        foreach ($denyRows as $row) {
            $userDenyPerms[$row['resource']][$row['action']] = true;
        }

        return [
            'acl'           => $acl,
            'roles'         => $roles,
            'userSpPerms'   => $userSpPerms,
            'userTbPerms'   => $userTbPerms,
            'userDenyPerms' => $userDenyPerms,
            'context'       => AclContext::withCompiled(
                snapshot:        $acl->getSnapshot(),
                compiler:        new EffectivePermissionCompiler(),
                userId:          $uid,
                roles:           $roles,
                authenticated:   true,
                userSpPerms:     $userSpPerms,
                userTbPerms:     $userTbPerms,
                userDenyPerms:   $userDenyPerms,
            ),
        ];
    }

    // --- ACL loader ---

    protected function loadAcl()
    {
        $aclFile = Config::get()['acl_file'];
        if (file_exists($aclFile)) {
            unlink($aclFile);
        }
        return include CONFIG_PATH . 'acl.php';
    }

    protected function dbSpPerms(): array
    {
        return withDefaultConnection(fn() =>
            DB::table('sp_permissions')->pluck('name') ?: []
        );
    }

    // --- Connection helper ---

    protected function withDb(callable $fn): mixed
    {
        return withDefaultConnection($fn);
    }

    // --- Destructive guard ---

    protected function requireConfirm(array $parsed): bool
    {
        if ($this->opt($parsed, 'force', false) || $this->opt($parsed, 'yes', false) || $this->opt($parsed, 'confirm', false)) {
            return true;
        }
        echo "✗ Operación destructiva. Agregue --force para confirmar.\n";
        return false;
    }

    // --- Output helpers ---

    protected function printDryRun(string $action): void
    {
        echo "  [dry-run] {$action}\n";
    }

    protected function printTable(array $rows, array $columns): void
    {
        if (empty($rows)) {
            echo "  (sin registros)\n";
            return;
        }

        $widths = [];
        foreach ($columns as $col) {
            $widths[$col] = strlen($col);
        }
        foreach ($rows as $row) {
            foreach ($columns as $col) {
                $val = (string)($row[$col] ?? '');
                if (strlen($val) > ($widths[$col] ?? 0)) {
                    $widths[$col] = strlen($val);
                }
            }
        }

        $separator = '+';
        foreach ($columns as $col) {
            $separator .= str_repeat('-', $widths[$col] + 2) . '+';
        }

        echo $separator . "\n";
        $header = '|';
        foreach ($columns as $col) {
            $header .= ' ' . str_pad($col, $widths[$col]) . ' |';
        }
        echo $header . "\n";
        echo $separator . "\n";

        foreach ($rows as $row) {
            $line = '|';
            foreach ($columns as $col) {
                $val   = (string)($row[$col] ?? '');
                $line .= ' ' . str_pad($val, $widths[$col]) . ' |';
            }
            echo $line . "\n";
        }

        echo $separator . "\n";
        echo "\n" . count($rows) . " registro(s) encontrado(s).\n";
    }
}
