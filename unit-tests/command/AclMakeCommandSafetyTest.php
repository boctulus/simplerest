<?php

namespace Boctulus\Simplerest\Tests;

use PHPUnit\Framework\TestCase;

final class AclMakeCommandSafetyTest extends TestCase
{
    public function testForceRegenerationUsesTransactionalRoleReconciliation(): void
    {
        $root = dirname(__DIR__, 2);
        $command = file_get_contents($root . '/app/Commands/acl/AclMakeCommand.php');
        $legacy = file_get_contents($root . '/app/Commands/make/BaseMakeCommand.php');
        $acl = file_get_contents($root . '/packages/boctulus/fine-grained-acl/src/Acl.php');

        $this->assertStringNotContainsString("DB::table('roles')", $command);
        $this->assertStringNotContainsString('unlink($acl_file)', $command);
        $this->assertStringNotContainsString('unlink($acl_file)', $legacy);
        $this->assertStringNotContainsString('unlink($aclFile)', $this->readBaseAclCommand($root));
        $this->assertStringContainsString("Config::set('acl_rebuild', true)", $command);
        $this->assertStringContainsString("Config::set('acl_defer_cache_write', true)", $command);
        $this->assertStringContainsString("Config::set('acl_rebuild', true)", $legacy);
        $this->assertStringContainsString("Config::set('acl_defer_cache_write', true)", $legacy);
        $this->assertStringContainsString('deferRoleCatalogPersistence($force)', $command);
        $this->assertStringContainsString('$acl->reconcileRoleCatalog()', $command);
        $this->assertStringContainsString('deferRoleCatalogPersistence($force)', $legacy);
        $this->assertStringContainsString('$acl->reconcileRoleCatalog()', $legacy);

        $this->assertStringContainsString('DB::transaction(', $acl);
        $this->assertStringContainsString('DB::table(\'user_roles\')->insert($assignment)', $acl);
        $this->assertStringContainsString(
            "ACL role reconciliation changed user_sp_permissions unexpectedly",
            $acl
        );
    }

    private function readBaseAclCommand(string $root): string
    {
        return file_get_contents($root . '/app/Commands/acl/BaseAclCommand.php');
    }
}
