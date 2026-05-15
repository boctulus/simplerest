<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Engine\AclEngine;
use Boctulus\Simplerest\Core\Security\Service\RoleHierarchyService;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

/**
 * Tests for lineage-based role hierarchy via RoleHierarchyService.
 *
 * These checks compare role position in the inheritance graph,
 * NOT effective permission sets. Use AclEngine::roleDominates() for
 * semantically correct capability comparison.
 * 
 * Ejecutar con: ./vendor/bin/phpunit unit-tests/acl/RoleHierarchyServiceTest.php
 */
class RoleHierarchyServiceTest extends TestCase
{
    private AclSnapshot $snapshot;
    private RoleHierarchyService $service;

    protected function setUp(): void
    {
        // guest → registered → supervisor → superadmin
        //                    ↘ usuario → usuario_plus → moderador
        $parentRoleNames = [
            'registered'  => 'guest',
            'supervisor'  => 'registered',
            'superadmin'  => 'supervisor',
            'usuario'     => 'registered',
            'usuario_plus'=> 'usuario',
            'moderador'   => 'usuario_plus',
        ];

        $this->snapshot = new AclSnapshot(
            rolePerms:       [],
            parentRoleNames: $parentRoleNames,
            validSpPerms:    [],
        );

        $this->service = new RoleHierarchyService($this->snapshot);
    }

    // ── getAncestry ───────────────────────────────────────────────────────

    public function test_superadmin_ancestry(): void
    {
        $this->assertEquals(
            ['supervisor', 'registered', 'guest'],
            $this->service->getAncestry('superadmin')
        );
    }

    public function test_guest_has_no_ancestry(): void
    {
        $this->assertEquals([], $this->service->getAncestry('guest'));
    }

    public function test_moderador_ancestry(): void
    {
        $this->assertEquals(
            ['usuario_plus', 'usuario', 'registered', 'guest'],
            $this->service->getAncestry('moderador')
        );
    }

    // ── isHigherRole ──────────────────────────────────────────────────────

    public function test_superadmin_is_higher_than_supervisor(): void
    {
        $this->assertTrue($this->service->isHigherRole('superadmin', 'supervisor'));
    }

    public function test_supervisor_is_higher_than_guest(): void
    {
        $this->assertTrue($this->service->isHigherRole('supervisor', 'guest'));
    }

    public function test_guest_is_not_higher_than_supervisor(): void
    {
        $this->assertFalse($this->service->isHigherRole('guest', 'supervisor'));
    }

    public function test_role_is_not_higher_than_itself(): void
    {
        $this->assertFalse($this->service->isHigherRole('supervisor', 'supervisor'));
    }

    public function test_different_branches_return_null(): void
    {
        $this->assertNull($this->service->isHigherRole('superadmin', 'moderador'));
        $this->assertNull($this->service->isHigherRole('moderador', 'superadmin'));
    }

    // ── hasRoleOrHigher ───────────────────────────────────────────────────

    public function test_superadmin_has_role_or_higher_supervisor(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->service->hasRoleOrHigher('supervisor', $ctx));
    }

    public function test_superadmin_has_role_or_higher_guest(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->service->hasRoleOrHigher('guest', $ctx));
    }

    public function test_guest_not_has_role_or_higher_supervisor(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->service->hasRoleOrHigher('supervisor', $ctx));
    }

    public function test_exact_role_counts_as_or_higher(): void
    {
        $ctx = new AclContext(roles: ['supervisor']);
        $this->assertTrue($this->service->hasRoleOrHigher('supervisor', $ctx));
    }

    // ── hasAnyRoleOrHigher ────────────────────────────────────────────────

    public function test_superadmin_has_any_role_or_higher(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->service->hasAnyRoleOrHigher(['moderador', 'supervisor'], $ctx));
    }

    public function test_guest_has_none_from_higher_roles(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->service->hasAnyRoleOrHigher(['supervisor', 'superadmin'], $ctx));
    }

    // ── hasRolePermissionsOrHigher ────────────────────────────────────────

    public function test_superadmin_has_role_permissions_or_higher_via_lineage(): void
    {
        $engine = new AclEngine($this->snapshot);
        $ctx    = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->service->hasRolePermissionsOrHigher($ctx, 'supervisor', $engine));
    }

    public function test_guest_lacks_role_permissions_or_higher(): void
    {
        $rolePerms = [
            'guest' => [
                'role_id'        => -1,
                'sp_permissions' => [],
                'tb_permissions' => [],
            ],
            'supervisor' => [
                'role_id'        => 502,
                'sp_permissions' => ['read_all'],
                'tb_permissions' => [],
            ],
        ];

        $snapshot = new AclSnapshot(
            rolePerms:       $rolePerms,
            parentRoleNames: ['supervisor' => 'guest'],
            validSpPerms:    ['read_all'],
        );

        $service = new RoleHierarchyService($snapshot);
        $engine   = new AclEngine($snapshot);

        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($service->hasRolePermissionsOrHigher($ctx, 'supervisor', $engine));
    }

    public function test_has_role_permissions_or_higher_exact_role(): void
    {
        $engine = new AclEngine($this->snapshot);
        $ctx    = new AclContext(roles: ['supervisor']);
        $this->assertTrue($this->service->hasRolePermissionsOrHigher($ctx, 'supervisor', $engine));
    }

    public function test_different_branches_falls_back_to_permission_check(): void
    {
        $rolePerms = [
            'editor' => [
                'role_id'        => 20,
                'sp_permissions' => ['read_all'],
                'tb_permissions' => [
                    'articles' => ['show', 'list', 'create', 'update'],
                ],
            ],
            'supervisor' => [
                'role_id'        => 502,
                'sp_permissions' => [],
                'tb_permissions' => [
                    'users' => ['show_all', 'list_all'],
                ],
            ],
        ];

        $snapshot = new AclSnapshot(
            rolePerms:       $rolePerms,
            parentRoleNames: [],
            validSpPerms:    ['read_all'],
        );

        $service = new RoleHierarchyService($snapshot);
        $engine   = new AclEngine($snapshot);

        // editor has read_all → can show any resource incl. users
        $ctx = new AclContext(roles: ['editor']);
        $this->assertTrue($service->hasRolePermissionsOrHigher($ctx, 'supervisor', $engine));

        // guest has no permissions → cannot match supervisor
        $ctx2 = new AclContext(roles: ['guest']);
        $this->assertFalse($service->hasRolePermissionsOrHigher($ctx2, 'supervisor', $engine));
    }

    public function test_nonexistent_target_role_returns_true(): void
    {
        $engine = new AclEngine($this->snapshot);
        $ctx    = new AclContext(roles: ['guest']);
        $this->assertTrue($this->service->hasRolePermissionsOrHigher($ctx, 'nonexistent', $engine));
    }
}
