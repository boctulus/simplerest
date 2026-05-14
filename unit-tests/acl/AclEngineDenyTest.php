<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Security\Compiler\EffectivePermissionCompiler;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Engine\AclEngine;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

class AclEngineDenyTest extends TestCase
{
    private AclSnapshot $snapshot;
    private EffectivePermissionCompiler $compiler;

    protected function setUp(): void
    {
        // vendedor can show/list/create/update/delete on products
        // editor has read_all
        $rolePerms = [
            'vendedor' => [
                'role_id'        => 1,
                'sp_permissions' => [],
                'tb_permissions' => [
                    'products' => ['show', 'list', 'create', 'update', 'delete'],
                ],
            ],
            'editor' => [
                'role_id'        => 2,
                'sp_permissions' => ['read_all'],
                'tb_permissions' => [],
            ],
        ];

        // Role-level explicit deny: vendedor cannot delete products
        $denyRolePerms = [
            'vendedor' => [
                'tb' => [
                    'products' => ['delete' => true],
                ],
                'sp' => [],
            ],
        ];

        $this->snapshot = new AclSnapshot(
            rolePerms:       $rolePerms,
            parentRoleNames: [],
            validSpPerms:    ['read_all', 'write_all', 'lock', 'impersonate'],
            denyRolePerms:   $denyRolePerms,
        );

        $this->compiler = new EffectivePermissionCompiler();
    }

    public function test_role_level_deny_blocks_role_level_allow_legacy(): void
    {
        $engine  = new AclEngine($this->snapshot);
        $context = new AclContext(roles: ['vendedor'], authenticated: true);

        $this->assertTrue($engine->can($context, 'create', 'products'));
        $this->assertFalse($engine->can($context, 'delete', 'products'));
    }

    public function test_role_level_deny_blocks_read_all_sentinel_legacy(): void
    {
        // editor has read_all but we explicitly deny show on products via role
        $snapshot = new AclSnapshot(
            rolePerms: [
                'editor' => [
                    'role_id'        => 2,
                    'sp_permissions' => ['read_all'],
                    'tb_permissions' => [],
                ],
            ],
            parentRoleNames: [],
            validSpPerms:    ['read_all'],
            denyRolePerms:   [
                'editor' => ['tb' => ['products' => ['show' => true]], 'sp' => []],
            ],
        );

        $engine  = new AclEngine($snapshot);
        $context = new AclContext(roles: ['editor'], authenticated: true);

        $this->assertFalse($engine->can($context, 'show', 'products'));
        $this->assertTrue($engine->can($context, 'list', 'products')); // not denied
    }

    public function test_user_level_deny_blocks_role_allow_legacy(): void
    {
        $engine  = new AclEngine($this->snapshot);
        $context = new AclContext(
            roles:         ['vendedor'],
            authenticated: true,
            userDenyPerms: ['products' => ['update' => true]],
        );

        $this->assertTrue($engine->can($context, 'create', 'products'));
        $this->assertFalse($engine->can($context, 'update', 'products'));
    }

    public function test_user_level_deny_is_per_action_legacy(): void
    {
        $engine  = new AclEngine($this->snapshot);
        $context = new AclContext(
            roles:         ['vendedor'],
            authenticated: true,
            userDenyPerms: ['products' => ['delete' => true]],
        );

        $this->assertTrue($engine->can($context, 'show', 'products'));
        $this->assertTrue($engine->can($context, 'list', 'products'));
        $this->assertFalse($engine->can($context, 'delete', 'products'));
    }

    public function test_legacy_replacement_semantics_unchanged_without_deny(): void
    {
        // No explicit deny — user_tb_permissions replacement still works
        $snapshot = new AclSnapshot(
            rolePerms: [
                'vendedor' => [
                    'role_id'        => 1,
                    'sp_permissions' => [],
                    'tb_permissions' => [
                        'products' => ['show', 'list', 'create', 'update', 'delete'],
                    ],
                ],
            ],
            parentRoleNames: [],
            validSpPerms:    [],
        );
        $engine = new AclEngine($snapshot);

        // user_tb_permissions: only 'show' bit (=8)
        $context = new AclContext(
            roles:         ['vendedor'],
            authenticated: true,
            userTbPerms:   ['products' => 8],
        );

        $this->assertTrue($engine->can($context, 'show', 'products'));
        $this->assertFalse($engine->can($context, 'create', 'products'));
    }

    public function test_compiled_path_applies_deny_precedence(): void
    {
        $context = AclContext::withCompiled(
            snapshot:      $this->snapshot,
            compiler:      $this->compiler,
            userId:        42,
            roles:         ['vendedor'],
            authenticated: true,
            userDenyPerms: ['products' => ['create' => true]],
        );

        $emptyEngine = new AclEngine(new AclSnapshot([], [], [])); // proves fast path only consults compiled
        $this->assertNotNull($context->compiledPermissions);
        $this->assertFalse($emptyEngine->can($context, 'create', 'products'));
        $this->assertFalse($emptyEngine->can($context, 'delete', 'products'));
        $this->assertTrue($emptyEngine->can($context, 'show', 'products'));
    }

    public function test_hasExplicitDeny_contract(): void
    {
        $engine  = new AclEngine($this->snapshot);
        $context = new AclContext(
            roles:         ['vendedor'],
            authenticated: true,
            userDenyPerms: ['products' => ['update' => true]],
        );

        $this->assertTrue($engine->hasExplicitDeny($context, 'update', 'products'));  // user-level
        $this->assertTrue($engine->hasExplicitDeny($context, 'delete', 'products'));  // role-level
        $this->assertFalse($engine->hasExplicitDeny($context, 'show', 'products'));
    }
}
