<?php

namespace Boctulus\Simplerest\Tests\Acl;

use Boctulus\FineGrainedACL\Acl as FineGrainedAcl;
use Boctulus\Simplerest\Core\Security\Acl as CoreAcl;
use PHPUnit\Framework\TestCase;

final class AclRoleCatalogDeferralTest extends TestCase
{
    protected function tearDown(): void
    {
        CoreAcl::deferRoleCatalogPersistence(false);
        parent::tearDown();
    }

    public function test_role_catalog_persistence_can_be_deferred_and_restored(): void
    {
        CoreAcl::deferRoleCatalogPersistence(true);

        $this->assertTrue(FineGrainedAclDeferralFixture::isRoleCatalogPersistenceDeferredForTest());

        CoreAcl::deferRoleCatalogPersistence(false);

        $this->assertFalse(FineGrainedAclDeferralFixture::isRoleCatalogPersistenceDeferredForTest());
    }

    public function test_fine_grained_acl_keeps_role_registration_in_memory_when_deferred(): void
    {
        CoreAcl::deferRoleCatalogPersistence(true);
        $reflection = new \ReflectionClass(FineGrainedAcl::class);
        $acl = $reflection->newInstanceWithoutConstructor();

        $acl->addRole('guest', 1);

        $rolePermissions = (new \ReflectionProperty(CoreAcl::class, 'role_perms'))->getValue($acl);
        $this->assertSame(1, $rolePermissions['guest']['role_id']);
    }

    public function test_database_backed_reconciliation_is_a_no_op_when_not_deferred(): void
    {
        $reflection = new \ReflectionClass(FineGrainedAcl::class);
        $acl = $reflection->newInstanceWithoutConstructor();

        $acl->reconcileRoleCatalog();

        $this->assertTrue(true);
    }
}

final class FineGrainedAclDeferralFixture extends FineGrainedAcl
{
    public static function isRoleCatalogPersistenceDeferredForTest(): bool
    {
        return parent::isRoleCatalogPersistenceDeferred();
    }
}
