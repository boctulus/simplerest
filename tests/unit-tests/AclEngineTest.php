<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;
use Boctulus\Simplerest\Core\Security\Engine\AclEngine;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

class AclEngineTest extends TestCase
{
    private AclEngine $engine;

    protected function setUp(): void
    {
        /*
         * Snapshot mirrors the example in docs/ACL.md:
         *
         * guest  -> products: [show_all, list_all]
         * vendedor inherits guest -> products: [show_all, list_all, create, update, delete]
         *                        -> foo: [create, list]
         * admin inherits guest  -> sp: [read_all, write_all]
         *                       -> products: [show_all, list_all]
         * superadmin inherits admin -> sp: [read_all, write_all, lock, fill_all]
         *                           -> products: [show_all, list_all]
         */
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
            'superadmin' => [
                'role_id'        => 500,
                'sp_permissions' => ['read_all', 'write_all', 'lock', 'fill_all'],
                'tb_permissions' => [
                    'products'    => ['show_all', 'list_all'],
                    'permissions' => ['show', 'list', 'create', 'update', 'delete'],
                ],
            ],
        ];

        $parentRoleNames = [
            'vendedor'   => 'guest',
            'admin'      => 'guest',
            'superadmin' => 'admin',
        ];

        $validSpPerms = [
            'read_all', 'read_all_folders', 'read_all_trashcan',
            'write_all', 'write_all_folders', 'write_all_trashcan',
            'write_all_collections', 'fill_all', 'grant', 'impersonate',
            'lock', 'transfer',
        ];

        $snapshot = new AclSnapshot(
            rolePerms:       $rolePerms,
            parentRoleNames: $parentRoleNames,
            validSpPerms:    $validSpPerms,
        );

        $this->engine = new AclEngine($snapshot);
    }

    // -------------------------------------------------------------------------
    // Resource permission tests
    // -------------------------------------------------------------------------

    public function test_guest_can_list_products(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertTrue($this->engine->hasResourcePermission('list_all', 'products', $ctx));
    }

    public function test_guest_cannot_create_products(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->engine->hasResourcePermission('create', 'products', $ctx));
    }

    public function test_vendedor_can_create_products(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertTrue($this->engine->hasResourcePermission('create', 'products', $ctx));
    }

    public function test_vendedor_can_list_foo(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertTrue($this->engine->hasResourcePermission('list', 'foo', $ctx));
    }

    public function test_vendedor_cannot_delete_foo(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertFalse($this->engine->hasResourcePermission('delete', 'foo', $ctx));
    }

    // -------------------------------------------------------------------------
    // Special permission tests
    // -------------------------------------------------------------------------

    public function test_admin_has_read_all(): void
    {
        $ctx = new AclContext(roles: ['admin']);
        $this->assertTrue($this->engine->hasSpecialPermission('read_all', $ctx));
    }

    public function test_guest_has_no_read_all(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->engine->hasSpecialPermission('read_all', $ctx));
    }

    public function test_superadmin_has_lock(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->engine->hasSpecialPermission('lock', $ctx));
    }

    public function test_user_sp_override_grants_permission(): void
    {
        $ctx = new AclContext(roles: ['vendedor'], userSpPerms: ['impersonate']);
        $this->assertTrue($this->engine->hasSpecialPermission('impersonate', $ctx));
    }

    // -------------------------------------------------------------------------
    // hasPermission (full evaluation) tests
    // -------------------------------------------------------------------------

    public function test_admin_can_show_any_resource_via_read_all(): void
    {
        $ctx = new AclContext(roles: ['admin']);
        $this->assertTrue($this->engine->can($ctx, 'show', 'orders'));
    }

    public function test_admin_can_delete_via_write_all(): void
    {
        $ctx = new AclContext(roles: ['admin']);
        $this->assertTrue($this->engine->can($ctx, 'delete', 'orders'));
    }

    public function test_vendedor_cannot_show_orders(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertFalse($this->engine->can($ctx, 'show', 'orders'));
    }

    // -------------------------------------------------------------------------
    // Role hierarchy tests
    // -------------------------------------------------------------------------

    public function test_superadmin_is_higher_than_admin(): void
    {
        $this->assertTrue($this->engine->isHigherRole('superadmin', 'admin'));
    }

    public function test_admin_is_higher_than_guest(): void
    {
        $this->assertTrue($this->engine->isHigherRole('admin', 'guest'));
    }

    public function test_guest_is_not_higher_than_admin(): void
    {
        $this->assertFalse($this->engine->isHigherRole('guest', 'admin'));
    }

    public function test_role_is_not_higher_than_itself(): void
    {
        $this->assertFalse($this->engine->isHigherRole('admin', 'admin'));
    }

    public function test_unrelated_roles_return_null(): void
    {
        $this->assertNull($this->engine->isHigherRole('vendedor', 'superadmin'));
    }

    public function test_superadmin_hasRoleOrHigher_admin(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->engine->hasRoleOrHigher('admin', $ctx));
    }

    public function test_guest_not_hasRoleOrHigher_admin(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->engine->hasRoleOrHigher('admin', $ctx));
    }

    public function test_hasAnyRole(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertTrue($this->engine->hasAnyRole(['admin', 'vendedor'], $ctx));
        $this->assertFalse($this->engine->hasAnyRole(['admin', 'superadmin'], $ctx));
    }

    public function test_hasAnyRoleOrHigher(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->engine->hasAnyRoleOrHigher(['guest', 'admin'], $ctx));
    }

    // -------------------------------------------------------------------------
    // Ancestry tests
    // -------------------------------------------------------------------------

    public function test_superadmin_ancestry(): void
    {
        $this->assertEquals(['admin', 'guest'], $this->engine->getAncestry('superadmin'));
    }

    public function test_guest_has_no_ancestry(): void
    {
        $this->assertEquals([], $this->engine->getAncestry('guest'));
    }
}
