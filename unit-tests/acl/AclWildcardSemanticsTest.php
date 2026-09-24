<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Security\Acl;
use Boctulus\Simplerest\Core\Security\Compiler\EffectivePermissionCompiler;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Engine\AclEngine;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

require_once __DIR__ . '/../../vendor/autoload.php';

class AclWildcardTestBuilder extends Acl
{
}

class AclWildcardSemanticsTest extends TestCase
{
    private function snapshot(array $allow, array $deny = []): AclSnapshot
    {
        return new AclSnapshot(
            rolePerms: [
                'probe' => [
                    'role_id' => 1,
                    'sp_permissions' => [],
                    'tb_permissions' => $allow,
                ],
            ],
            parentRoleNames: [],
            validSpPerms: [],
            denyRolePerms: $deny
        );
    }

    public function test_compiler_rejects_literal_star_resource_grants(): void
    {
        $snapshot = $this->snapshot(['*' => ['show']]);

        $this->expectException(\InvalidArgumentException::class);
        AclContext::withCompiled(
            snapshot: $snapshot,
            compiler: new EffectivePermissionCompiler(),
            userId: 1,
            roles: ['probe'],
            authenticated: true
        );
    }

    public function test_role_compilation_rejects_literal_star_resource_grants(): void
    {
        $snapshot = $this->snapshot(['*' => ['show']]);

        $this->expectException(\InvalidArgumentException::class);
        (new EffectivePermissionCompiler())->compileRoles(
            $snapshot->rolePerms,
            $snapshot->denyRolePerms,
            $snapshot->validSpPerms
        );
    }

    public function test_user_table_mask_cannot_target_reserved_star_resource(): void
    {
        $snapshot = $this->snapshot([]);

        $this->expectException(\InvalidArgumentException::class);
        AclContext::withCompiled(
            snapshot: $snapshot,
            compiler: new EffectivePermissionCompiler(),
            userId: 1,
            roles: ['probe'],
            authenticated: true,
            userTbPerms: ['*' => 2]
        );
    }

    public function test_acl_builder_rejects_literal_star_resource_grants(): void
    {
        $builder = (new \ReflectionClass(AclWildcardTestBuilder::class))
            ->newInstanceWithoutConstructor();
        $builder->addRole('probe', 1);

        $this->expectException(\InvalidArgumentException::class);
        $builder->addResourcePermissions('*', ['show']);
    }

    public function test_legacy_context_does_not_expand_literal_star_to_other_resources(): void
    {
        $snapshot = $this->snapshot(['*' => ['show']]);
        $context = new AclContext(roles: ['probe'], authenticated: true);
        $engine = new AclEngine($snapshot);

        $this->assertFalse($engine->can($context, 'show', 'products'));
        $this->assertFalse($engine->hasResourcePermission('show', 'products', $context));
    }

    public function test_wildcard_deny_blocks_both_compiled_permission_paths(): void
    {
        $snapshot = $this->snapshot(
            ['products' => ['show']],
            ['probe' => ['tb' => ['*' => ['show' => true]], 'sp' => []]]
        );
        $context = AclContext::withCompiled(
            snapshot: $snapshot,
            compiler: new EffectivePermissionCompiler(),
            userId: 1,
            roles: ['probe'],
            authenticated: true
        );
        $engine = new AclEngine($snapshot);

        $this->assertFalse($engine->can($context, 'show', 'products'));
        $this->assertFalse($engine->hasResourcePermission('show', 'products', $context));
    }
}
