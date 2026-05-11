<?php

use PHPUnit\Framework\TestCase;
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

/**
 * Tests for lineage-based role hierarchy via AclEngine.
 *
 * These checks compare role position in the inheritance graph,
 * NOT effective permission sets. Use AclEngine::roleDominates() for
 * semantically correct capability comparison.
 */
class RoleHierarchyServiceTest extends TestCase
{
    private AclEngine $engine;

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

        $this->engine = new AclEngine(new AclSnapshot(
            rolePerms:       [],
            parentRoleNames: $parentRoleNames,
            validSpPerms:    [],
        ));
    }

    // ── getAncestry ───────────────────────────────────────────────────────

    public function test_superadmin_ancestry(): void
    {
        $this->assertEquals(
            ['supervisor', 'registered', 'guest'],
            $this->engine->getAncestry('superadmin')
        );
    }

    public function test_guest_has_no_ancestry(): void
    {
        $this->assertEquals([], $this->engine->getAncestry('guest'));
    }

    public function test_moderador_ancestry(): void
    {
        $this->assertEquals(
            ['usuario_plus', 'usuario', 'registered', 'guest'],
            $this->engine->getAncestry('moderador')
        );
    }

    // ── isHigherRole ──────────────────────────────────────────────────────

    public function test_superadmin_is_higher_than_supervisor(): void
    {
        $this->assertTrue($this->engine->isHigherRole('superadmin', 'supervisor'));
    }

    public function test_supervisor_is_higher_than_guest(): void
    {
        $this->assertTrue($this->engine->isHigherRole('supervisor', 'guest'));
    }

    public function test_guest_is_not_higher_than_supervisor(): void
    {
        $this->assertFalse($this->engine->isHigherRole('guest', 'supervisor'));
    }

    public function test_role_is_not_higher_than_itself(): void
    {
        $this->assertFalse($this->engine->isHigherRole('supervisor', 'supervisor'));
    }

    public function test_different_branches_return_null(): void
    {
        $this->assertNull($this->engine->isHigherRole('superadmin', 'moderador'));
        $this->assertNull($this->engine->isHigherRole('moderador', 'superadmin'));
    }

    // ── hasRoleOrHigher ───────────────────────────────────────────────────

    public function test_superadmin_has_role_or_higher_supervisor(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->engine->hasRoleOrHigher('supervisor', $ctx));
    }

    public function test_superadmin_has_role_or_higher_guest(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->engine->hasRoleOrHigher('guest', $ctx));
    }

    public function test_guest_not_has_role_or_higher_supervisor(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->engine->hasRoleOrHigher('supervisor', $ctx));
    }

    public function test_exact_role_counts_as_or_higher(): void
    {
        $ctx = new AclContext(roles: ['supervisor']);
        $this->assertTrue($this->engine->hasRoleOrHigher('supervisor', $ctx));
    }

    // ── hasAnyRoleOrHigher ────────────────────────────────────────────────

    public function test_superadmin_has_any_role_or_higher(): void
    {
        $ctx = new AclContext(roles: ['superadmin']);
        $this->assertTrue($this->engine->hasAnyRoleOrHigher(['moderador', 'supervisor'], $ctx));
    }

    public function test_guest_has_none_from_higher_roles(): void
    {
        $ctx = new AclContext(roles: ['guest']);
        $this->assertFalse($this->engine->hasAnyRoleOrHigher(['supervisor', 'superadmin'], $ctx));
    }
}
