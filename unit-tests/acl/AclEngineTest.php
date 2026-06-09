<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationPolicyInterface;
use Boctulus\Simplerest\Core\Security\Contracts\AuthorizationServiceInterface;
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

/*
 *
 * Ejecutar con: ./vendor/bin/phpunit unit-tests/acl/AclEngineTest.php
 */
class AclEngineTest extends TestCase
{
    private AclEngine $engine;

    protected function setUp(): void
    {
        /*
         * Snapshot mirrors the example in docs/ACL.md:
         *
         * guest     → products: [show_all, list_all]
         * vendedor  → inherits guest + products: [create, update, delete] + foo: [create, list]
         * admin     → inherits guest + sp: [read_all, write_all]
         * superadmin→ inherits admin + sp: [lock, fill_all]
         * support   → sp: [impersonate] (no write_all — lateral role, different branch)
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
            'support' => [
                'role_id'        => 10,
                'sp_permissions' => ['impersonate'],
                'tb_permissions' => [],
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

        $this->engine = new AclEngine(new AclSnapshot(
            rolePerms:       $rolePerms,
            parentRoleNames: $parentRoleNames,
            validSpPerms:    $validSpPerms,
        ));
    }

    // ── Resource permission ────────────────────────────────────────────────

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

    public function test_vendedor_cannot_delete_foo(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertFalse($this->engine->hasResourcePermission('delete', 'foo', $ctx));
    }

    // ── Special permission ─────────────────────────────────────────────────

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

    public function test_user_sp_override_grants_permission(): void
    {
        $ctx = new AclContext(roles: ['vendedor'], userSpPerms: ['impersonate']);
        $this->assertTrue($this->engine->hasSpecialPermission('impersonate', $ctx));
    }

    // ── can() full evaluation ─────────────────────────────────────────────

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

    // ── Role membership ────────────────────────────────────────────────────

    public function test_hasRole(): void
    {
        $ctx = new AclContext(roles: ['admin']);
        $this->assertTrue($this->engine->hasRole('admin', $ctx));
        $this->assertFalse($this->engine->hasRole('superadmin', $ctx));
    }

    public function test_hasAnyRole(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertTrue($this->engine->hasAnyRole(['admin', 'vendedor'], $ctx));
        $this->assertFalse($this->engine->hasAnyRole(['admin', 'superadmin'], $ctx));
    }

    // ── hasAllPermissions ─────────────────────────────────────────────────

    public function test_vendedor_has_all_product_permissions(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertTrue($this->engine->hasAllPermissions($ctx, [
            'products.create',
            'products.list_all',
        ]));
    }

    public function test_vendedor_fails_all_when_one_missing(): void
    {
        $ctx = new AclContext(roles: ['vendedor']);
        $this->assertFalse($this->engine->hasAllPermissions($ctx, [
            'products.create',
            'orders.create',   // not granted
        ]));
    }

    public function test_admin_has_all_via_write_all_sp(): void
    {
        $ctx = new AclContext(roles: ['admin']);
        $this->assertTrue($this->engine->hasAllPermissions($ctx, [
            'orders.delete',
            'users.create',
            'read_all',
        ]));
    }

    // ── hasAnyPermission ──────────────────────────────────────────────────

    public function test_guest_has_any_when_one_matches(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertTrue($this->engine->hasAnyPermission($ctx, [
            'products.list_all',
            'orders.create',    // not granted
        ]));
    }

    public function test_guest_has_none_when_none_match(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->engine->hasAnyPermission($ctx, [
            'orders.create',
            'users.delete',
        ]));
    }

    public function test_has_any_with_sp_permission(): void
    {
        $ctx = new AclContext(roles: ['support']);
        $this->assertTrue($this->engine->hasAnyPermission($ctx, [
            'write_all',    // support does NOT have this
            'impersonate',  // support HAS this
        ]));
    }

    // ── roleDominates (set-theory) ────────────────────────────────────────

    public function test_superadmin_dominates_admin(): void
    {
        $this->assertTrue($this->engine->roleDominates('superadmin', 'admin'));
    }

    public function test_admin_does_not_dominate_superadmin(): void
    {
        // superadmin has lock + fill_all that admin doesn't
        $this->assertFalse($this->engine->roleDominates('admin', 'superadmin'));
    }

    public function test_role_dominates_itself(): void
    {
        $this->assertTrue($this->engine->roleDominates('admin', 'admin'));
    }

    public function test_support_does_not_dominate_admin(): void
    {
        // support has impersonate but not read_all/write_all
        $this->assertFalse($this->engine->roleDominates('support', 'admin'));
    }

    public function test_admin_does_not_dominate_support(): void
    {
        // admin has no impersonate
        $this->assertFalse($this->engine->roleDominates('admin', 'support'));
    }

    public function test_any_role_dominates_guest_if_superset(): void
    {
        // vendedor has products: [show_all, list_all, create, update, delete]
        // guest has products: [show_all, list_all]
        // vendedor is a superset → dominates
        $this->assertTrue($this->engine->roleDominates('vendedor', 'guest'));
    }

    public function test_guest_does_not_dominate_vendedor(): void
    {
        $this->assertFalse($this->engine->roleDominates('guest', 'vendedor'));
    }

    public function test_role_dominates_empty_role(): void
    {
        // Any role dominates a role with zero permissions (vacuous truth)
        $this->assertTrue($this->engine->roleDominates('guest', 'nonexistent_role'));
    }

    // ── satisfiesPolicy ───────────────────────────────────────────────────

    public function test_satisfies_policy_when_can(): void
    {
        $ctx = new AclContext(roles: ['admin']);

        $policy = new class implements AuthorizationPolicyInterface {
            public function isSatisfiedBy(
                \Boctulus\Simplerest\Core\Security\Domain\AclContext $context,
                AuthorizationServiceInterface $engine
            ): bool {
                return $engine->can($context, 'delete', 'users');
            }
        };

        $this->assertTrue($this->engine->satisfiesPolicy($ctx, $policy));
    }

    public function test_fails_policy_when_cannot(): void
    {
        $ctx = new AclContext(roles: ['guest']);

        $policy = new class implements AuthorizationPolicyInterface {
            public function isSatisfiedBy(
                \Boctulus\Simplerest\Core\Security\Domain\AclContext $context,
                AuthorizationServiceInterface $engine
            ): bool {
                return $engine->can($context, 'delete', 'users');
            }
        };

        $this->assertFalse($this->engine->satisfiesPolicy($ctx, $policy));
    }
}
