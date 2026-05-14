<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Security\Compiler\EffectivePermissionCompiler;
use Boctulus\Simplerest\Core\Security\Compiler\TbPermissionBits;
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

class AclCompiledPermissionsTest extends TestCase
{
    private AclSnapshot $snapshot;
    private EffectivePermissionCompiler $compiler;

    protected function setUp(): void
    {
        $rolePerms = [
            'guest' => [
                'role_id'        => -1,
                'sp_permissions' => [],
                'tb_permissions' => [
                    'products' => ['show_all', 'list_all'],
                ],
            ],
            'vendedor' => [
                'role_id'        => 1,
                'sp_permissions' => [],
                'tb_permissions' => [
                    'products' => ['show_all', 'list_all', 'create', 'update', 'delete'],
                    'foo'      => ['create', 'list'],
                ],
            ],
            'admin' => [
                'role_id'        => 100,
                'sp_permissions' => ['read_all', 'write_all'],
                'tb_permissions' => [
                    'products' => ['show_all', 'list_all'],
                ],
            ],
        ];

        $this->snapshot = new AclSnapshot(
            rolePerms:       $rolePerms,
            parentRoleNames: ['vendedor' => 'guest', 'admin' => 'guest'],
            validSpPerms:    ['read_all', 'write_all', 'lock', 'impersonate'],
        );

        $this->compiler = new EffectivePermissionCompiler();
    }

    public function test_compile_roles_emits_per_role_allows(): void
    {
        $out = $this->compiler->compileRoles(
            $this->snapshot->rolePerms,
            [],
            $this->snapshot->validSpPerms
        );

        $this->assertTrue($out['allows']['guest']['products']['show_all']);
        $this->assertTrue($out['allows']['vendedor']['products']['create']);
        $this->assertTrue($out['allows']['admin']['__sp__']['read_all']);
    }

    public function test_compile_for_user_aggregates_role_perms(): void
    {
        $cp = $this->compiler->compileForUser($this->snapshot, ['vendedor']);

        $this->assertTrue(isset($cp['allow']['products']['create']));
        $this->assertTrue(isset($cp['allow']['foo']['list']));
        $this->assertFalse(isset($cp['allow']['products']['nonexistent']));
    }

    public function test_compile_for_user_replacement_semantics(): void
    {
        // packed bitmask: only 'show' bit set
        $packed = TbPermissionBits::SHOW;

        $cp = $this->compiler->compileForUser(
            $this->snapshot,
            ['vendedor'],
            [],
            ['products' => $packed]
        );

        // Replacement: only 'show' should remain for products
        $this->assertTrue(isset($cp['allow']['products']['show']));
        $this->assertFalse(isset($cp['allow']['products']['create']));
        $this->assertFalse(isset($cp['allow']['products']['delete']));

        // 'foo' is untouched
        $this->assertTrue(isset($cp['allow']['foo']['create']));
    }

    public function test_compile_for_user_additive_user_sp(): void
    {
        $cp = $this->compiler->compileForUser(
            $this->snapshot,
            ['guest'],
            ['lock']
        );

        $this->assertTrue(isset($cp['allow']['__sp__']['lock']));
    }

    public function test_read_all_sentinel_grants_any_read(): void
    {
        $cp = $this->compiler->compileForUser($this->snapshot, ['admin']);

        $this->assertTrue(isset($cp['allow']['*']['show']));
        $this->assertTrue(isset($cp['allow']['*']['list_all']));

        // write_all also active
        $this->assertTrue(isset($cp['allow']['*']['create']));
        $this->assertTrue(isset($cp['allow']['*']['delete']));
    }

    public function test_engine_fast_path_uses_compiled_permissions(): void
    {
        // Empty snapshot — engine MUST rely solely on compiledPermissions
        $emptySnapshot = new AclSnapshot(
            rolePerms:       [],
            parentRoleNames: [],
            validSpPerms:    [],
        );
        $engine = new AclEngine($emptySnapshot);

        $context = new AclContext(
            roles:               ['vendedor'],
            authenticated:       true,
            compiledPermissions: [
                'allow' => ['products' => ['show' => true]],
                'deny'  => [],
            ],
        );

        $this->assertTrue($engine->can($context, 'show', 'products'));
        $this->assertFalse($engine->can($context, 'delete', 'products'));
    }

    public function test_legacy_path_when_compiled_permissions_null(): void
    {
        $engine = new AclEngine($this->snapshot);

        $context = new AclContext(
            roles:         ['vendedor'],
            authenticated: true,
        );

        $this->assertTrue($engine->can($context, 'create', 'products'));
        $this->assertTrue($engine->can($context, 'list', 'foo'));
        $this->assertFalse($engine->can($context, 'delete', 'foo'));
    }

    public function test_snapshot_deserialization_with_missing_fields(): void
    {
        // Simulate a legacy snapshot that lacks the new compiled fields.
        $legacy = new AclSnapshot(
            rolePerms:       ['guest' => ['role_id' => -1, 'sp_permissions' => [], 'tb_permissions' => []]],
            parentRoleNames: [],
            validSpPerms:    [],
        );

        $this->assertSame([], $legacy->effectiveAllows);
        $this->assertSame([], $legacy->effectiveDenies);
        $this->assertSame([], $legacy->denyRolePerms);
        $this->assertSame([], $legacy->permissionExplanations);
    }

    public function test_explanations_are_deterministic(): void
    {
        $out1 = $this->compiler->compileRoles($this->snapshot->rolePerms, [], $this->snapshot->validSpPerms);
        $out2 = $this->compiler->compileRoles($this->snapshot->rolePerms, [], $this->snapshot->validSpPerms);

        $this->assertEquals($out1['explanations'], $out2['explanations']);
        $this->assertArrayHasKey('vendedor.products.create', $out1['explanations']);
        $this->assertTrue($out1['explanations']['vendedor.products.create']['granted']);
    }
}
