<?php

namespace Boctulus\Simplerest\Tests\Acl;

use Boctulus\FineGrainedACL\Acl as FineGrainedAcl;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Security\Acl as CoreAcl;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class FineGrainedAclRoleCatalogIntegrationTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_new_roles_preserve_existing_foreign_key_references(): void
    {
        $pdo = $this->createIsolatedDatabase([3, 1], true);
        $acl = $this->newDeferredAcl();
        $acl->addRole('guest', 1)->addRole('manager', 3)->addRole('administrator', 4);

        $acl->reconcileRoleCatalog();

        $roles = DB::table('roles')->orderBy('id', 'ASC')->get();
        $assignments = DB::table('user_roles')->orderBy('id', 'ASC')->get();
        $specialAssignments = DB::table('user_sp_permissions')->get();

        $this->assertSame([
            ['id' => 1, 'name' => 'guest'],
            ['id' => 3, 'name' => 'manager'],
            ['id' => 4, 'name' => 'administrator'],
        ], array_map(
            static fn (array $role): array => ['id' => (int) $role['id'], 'name' => $role['name']],
            $roles
        ));
        $this->assertSame([3, 1], array_map(
            static fn (array $assignment): int => (int) $assignment['role_id'],
            $assignments
        ));
        $this->assertSame([21], array_map(
            static fn (array $assignment): int => (int) $assignment['id'],
            $specialAssignments
        ));
        $this->assertSame(3, (int) $pdo->query('SELECT role_id FROM employees WHERE id = 30')->fetchColumn());
        $this->assertTrue($pdo->inTransaction() === false);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_role_id_change_is_rejected_without_mutating_references(): void
    {
        $pdo = $this->createIsolatedDatabase([3, 1], true);
        $acl = $this->newDeferredAcl();
        $acl->addRole('guest', 1)->addRole('manager', 4);

        try {
            $acl->reconcileRoleCatalog();
            $this->fail('Expected reconciliation to reject changing a persisted role id.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('migrate application references before changing its id', $exception->getMessage());
        }

        $roles = DB::table('roles')->orderBy('id', 'ASC')->get();
        $assignments = DB::table('user_roles')->orderBy('id', 'ASC')->get();

        $this->assertSame([1, 3], array_map(
            static fn (array $role): int => (int) $role['id'],
            $roles
        ));
        $this->assertSame([3, 1], array_map(
            static fn (array $assignment): int => (int) $assignment['role_id'],
            $assignments
        ));
        $this->assertSame(3, (int) $pdo->query('SELECT role_id FROM employees WHERE id = 30')->fetchColumn());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_role_removal_is_rejected_when_application_rows_reference_it(): void
    {
        $pdo = $this->createIsolatedDatabase([], true);
        $acl = $this->newDeferredAcl();
        $acl->addRole('guest', 1);

        try {
            $acl->reconcileRoleCatalog();
            $this->fail('Expected reconciliation to reject removing a role with application references.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('migrate application references before removing it', $exception->getMessage());
        }

        $roles = DB::table('roles')->orderBy('id', 'ASC')->get();

        $this->assertSame([1, 3], array_map(
            static fn (array $role): int => (int) $role['id'],
            $roles
        ));
        $this->assertSame(3, (int) $pdo->query('SELECT role_id FROM employees WHERE id = 30')->fetchColumn());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_unmapped_user_role_aborts_before_catalog_mutation(): void
    {
        $this->createIsolatedDatabase([99], false);
        $acl = $this->newDeferredAcl();
        $acl->addRole('guest', 1)->addRole('manager', 4);

        try {
            $acl->reconcileRoleCatalog();
            $this->fail('Expected reconciliation to reject an unmapped role assignment.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('has no unambiguous role-name mapping', $exception->getMessage());
        }

        $roles = DB::table('roles')->orderBy('id', 'ASC')->get();
        $assignments = DB::table('user_roles')->get();

        $this->assertSame([1, 3], array_map(
            static fn (array $role): int => (int) $role['id'],
            $roles
        ));
        $this->assertSame(99, (int) $assignments[0]['role_id']);
    }

    private function newDeferredAcl(): FineGrainedAcl
    {
        CoreAcl::deferRoleCatalogPersistence(true);
        $acl = (new \ReflectionClass(FineGrainedAcl::class))->newInstanceWithoutConstructor();
        $setup = new \ReflectionMethod(FineGrainedAcl::class, 'setup');
        $setup->setAccessible(true);
        $setup->invoke($acl);

        return $acl;
    }

    private function createIsolatedDatabase(array $assignedRoleIds, bool $withRoleForeignKey): \PDO
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO SQLite is required for the isolated role-catalog test.');
        }

        $root = dirname(__DIR__, 2);
        require_once $root . '/config/constants.php';
        require_once $root . '/src/framework/Helpers/db.php';

        $config = [
            'db_connection_default' => 'main',
            'db_connections' => [
                'main' => [
                    'driver' => 'sqlite',
                    'db_name' => ':memory:',
                    'tb_prefix' => null,
                    'pdo_options' => [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
                ],
            ],
            'namespace' => 'Boctulus\\Simplerest',
            'error_handling' => false,
            'users_table' => 'users',
        ];
        (new \ReflectionProperty(Config::class, 'data'))->setValue(null, $config);

        DB::setConnection('main');
        $pdo = DB::getConnection();
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('CREATE TABLE roles (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(50) NOT NULL)');
        $pdo->exec('CREATE TABLE sp_permissions (id INTEGER PRIMARY KEY, name VARCHAR(80) NOT NULL)');

        $roleForeignKey = $withRoleForeignKey
            ? ', FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT'
            : '';
        $pdo->exec(
            'CREATE TABLE user_roles ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, '
            . 'user_id INTEGER NOT NULL, '
            . 'role_id INTEGER NOT NULL, '
            . 'created_by INTEGER NULL, '
            . 'created_at TEXT NOT NULL, '
            . 'updated_by INTEGER NULL, '
            . 'updated_at TEXT NULL'
            . $roleForeignKey
            . ')'
        );
        $pdo->exec(
            'CREATE TABLE user_sp_permissions ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, '
            . 'sp_permission_id INTEGER NOT NULL, '
            . 'user_id INTEGER NOT NULL, '
            . 'created_by INTEGER NULL, '
            . 'created_at TEXT NULL, '
            . 'updated_by INTEGER NULL, '
            . 'updated_at TEXT NULL'
            . ')'
        );

        $pdo->exec("INSERT INTO sp_permissions (id, name) VALUES (1, 'read_all')");
        $pdo->exec("INSERT INTO roles (id, name) VALUES (1, 'guest'), (3, 'manager')");
        $pdo->exec(
            'CREATE TABLE employees ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, '
            . 'role_id INTEGER NULL, '
            . 'FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT'
            . ')'
        );
        $pdo->exec('INSERT INTO employees (id, role_id) VALUES (30, 3)');

        $assignmentInsert = $pdo->prepare(
            'INSERT INTO user_roles (id, user_id, role_id, created_by, created_at, updated_by, updated_at) '
            . 'VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        foreach ($assignedRoleIds as $index => $roleId) {
            $assignmentInsert->execute([
                10 + $index,
                50 + $index,
                $roleId,
                null,
                '2026-10-22 12:00:00',
                null,
                null,
            ]);
        }

        $pdo->exec(
            'INSERT INTO user_sp_permissions (id, sp_permission_id, user_id) VALUES (21, 1, 50)'
        );

        return $pdo;
    }
}
