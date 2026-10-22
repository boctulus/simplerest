<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;

/*
    * El bootstrap de esta suite configura dos aliases SQLite :memory: sin cargar app.php.
    * Estos tests verifican que las transacciones funcionan correctamente,
    * incluyendo commits, rollbacks y transacciones anidadas con savepoints.
    *
    * Requiere PHPUnit y la extensión PDO SQLite.
    *
    * Ejecuta con: ./vendor/bin/phpunit --no-configuration --bootstrap unit-tests/query-builder/DB_TransactionBootstrap.php unit-tests/query-builder/DB_TransactionTest.php
    *
*/
class DB_TransactionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/DB_TransactionBootstrap.php';

        $testConfig = Config::get('db_connections.test_sqlite');
        if (($testConfig['driver'] ?? null) !== 'sqlite' || ($testConfig['db_name'] ?? null) !== ':memory:') {
            throw new \RuntimeException('DB transaction tests require the isolated SQLite :memory: alias.');
        }

        Config::set('db_connections.test_sqlite_secondary', [
            'driver' => 'sqlite',
            'db_name' => ':memory:',
            'host' => null,
            'port' => null,
            'user' => null,
            'pass' => null,
            'charset' => null,
            'pdo_options' => null,
        ]);
    }

    public function setUp(): void
    {
        DB::setConnection('test_sqlite');
        $connection = DB::getConnection();
        if ($connection->getAttribute(\PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new \RuntimeException('DB transaction tests did not connect through SQLite.');
        }

        $this->ensureFixtureTable($connection);
    }

    public static function tearDownAfterClass(): void
    {
        DB::closeAllConnections();
    }

    public function tearDown(): void
    {
        DB::setConnection('test_sqlite');
        $this->ensureFixtureTable(DB::getConnection());
        DB::statement("DELETE FROM test_transactions WHERE is_test_data = TRUE");
    }

    private function ensureFixtureTable(\PDO $connection): void
    {
        $connection->exec("CREATE TABLE IF NOT EXISTS test_transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            is_test_data BOOLEAN NOT NULL DEFAULT TRUE
        )");
    }

    public function test_commit_persists()
    {
        DB::transaction(function() {
            DB::insert("INSERT INTO test_transactions (name, is_test_data) VALUES (?, TRUE)", ['commit_persists']);
        });

        $rows = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['commit_persists']);
        $this->assertNotEmpty($rows, "La fila debe existir después de commit");
    }

    public function test_rollback_on_exception()
    {
        $this->expectException(\Exception::class);

        try {
            DB::transaction(function() {
                DB::insert("INSERT INTO test_transactions (name, is_test_data) VALUES (?, TRUE)", ['should_rollback']);
                // forzamos excepción
                throw new \Exception("forcing rollback");
            });
        } finally {
            // Comprobamos que no quedó la fila
            $rows = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['should_rollback']);
            $this->assertEmpty($rows, "La fila no debe persistir después de rollback por excepción");
        }
    }

    public function test_nested_transaction_inner_rollback_keeps_outer()
    {
        // Escenario:
        // 1) begin outer
        // 2) insert A
        // 3) begin inner
        // 4) insert B
        // 5) rollback inner
        // 6) commit outer
        //
        // Resultado esperado con savepoints: A persiste, B no persiste.

        DB::beginTransaction();
        try {
            DB::insert("INSERT INTO test_transactions (name, is_test_data) VALUES (?, TRUE)", ['outer_A']);

            // inner
            DB::beginTransaction();
            try {
                DB::insert("INSERT INTO test_transactions (name, is_test_data) VALUES (?, TRUE)", ['inner_B']);
                // force rollback inner
                DB::rollback();
            } catch (\Exception $e) {
                // ensure inner rollback
                DB::rollback();
                throw $e;
            }

            // commit outer
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        $rowsA = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['outer_A']);
        $rowsB = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['inner_B']);

        $this->assertNotEmpty($rowsA, "Fila outer_A debe persistir tras commit externo");
        $this->assertEmpty($rowsB, "Fila inner_B no debe persistir tras rollback interno (savepoint)");
    }

    public function test_failed_begin_does_not_poison_state_or_rollback_external_transaction(): void
    {
        DB::setConnection('test_sqlite');
        $connection = DB::getConnection();
        $connection->beginTransaction();

        try {
            $callbackInvoked = false;
            $originalException = null;

            try {
                DB::transaction(function () use (&$callbackInvoked): void {
                    $callbackInvoked = true;
                });
            } catch (\Throwable $e) {
                $originalException = $e;
            }

            $this->assertInstanceOf(\PDOException::class, $originalException);
            $this->assertFalse($callbackInvoked, 'El callback no debe ejecutarse si BEGIN falla');
            $this->assertTrue(
                $connection->inTransaction(),
                'El rollback del scope fallido no debe deshacer una transacción externa'
            );

            try {
                DB::setConnection('test_sqlite_secondary');
                $this->fail('Debe rechazarse el cambio cuando PDO tiene una transacción externa activa');
            } catch (\LogicException $e) {
                $this->assertStringContainsString('active transaction', $e->getMessage());
            }
        } finally {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            if (DB::getCurrentConnectionId() !== 'test_sqlite') {
                DB::setConnection('test_sqlite');
            }
        }

        DB::beginTransaction();
        try {
            $this->assertTrue($connection->inTransaction(), 'Una transacción nueva debe poder iniciarse tras el fallo');
        } finally {
            DB::rollback();
        }
    }

    public function test_transaction_state_is_connection_scoped_and_connection_switch_is_rejected(): void
    {
        DB::setConnection('test_sqlite');
        $primaryConnection = DB::getConnection();
        DB::beginTransaction();

        try {
            try {
                DB::setConnection('test_sqlite_secondary');
                $this->fail('Debe rechazarse el cambio de conexión durante una transacción activa');
            } catch (\LogicException $e) {
                $this->assertStringContainsString('active transaction', $e->getMessage());
            }

            $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
            $this->assertTrue($primaryConnection->inTransaction());
            DB::commit();
            $this->assertFalse($primaryConnection->inTransaction());
        } finally {
            DB::rollback();
            if ($primaryConnection->inTransaction()) {
                $primaryConnection->rollBack();
            }

            if (DB::getCurrentConnectionId() !== 'test_sqlite') {
                DB::setConnection('test_sqlite');
            }
        }

        DB::setConnection('test_sqlite_secondary');
        $secondaryConnection = DB::getConnection();
        DB::beginTransaction();

        try {
            try {
                DB::getConnection('test_sqlite');
                $this->fail('Debe rechazarse getConnection() hacia otra conexión durante una transacción activa');
            } catch (\LogicException $e) {
                $this->assertStringContainsString('active transaction', $e->getMessage());
            }
        } finally {
            DB::rollback();
            if ($secondaryConnection->inTransaction()) {
                $secondaryConnection->rollBack();
            }

            if (DB::getCurrentConnectionId() !== 'test_sqlite') {
                DB::setConnection('test_sqlite');
            }
        }
    }

    public function test_connection_closure_is_rejected_while_managed_transaction_is_active(): void
    {
        DB::setConnection('test_sqlite');
        $primaryConnection = DB::getConnection();
        DB::getConnection('test_sqlite_secondary');
        DB::setConnection('test_sqlite');
        DB::beginTransaction();

        try {
            foreach ([
                static fn() => DB::closeConnection(),
                static fn() => DB::closeConnection('test_sqlite'),
                static fn() => DB::closeAllConnections(),
            ] as $close) {
                try {
                    $close();
                    $this->fail('No se debe cerrar una conexión con una transacción activa');
                } catch (\LogicException $e) {
                    $this->assertStringContainsString('active transaction', $e->getMessage());
                }
            }

            $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
            $this->assertSame($primaryConnection, DB::getConnection());
            $this->assertTrue($primaryConnection->inTransaction());
        } finally {
            DB::rollback();
        }

        DB::closeConnection('test_sqlite');
        $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
        DB::closeAllConnections();
        $this->assertNotSame($primaryConnection, DB::getConnection());
    }

    public function test_connection_closure_detects_transaction_started_directly_on_pdo(): void
    {
        DB::setConnection('test_sqlite');
        $connection = DB::getConnection();
        $connection->beginTransaction();

        try {
            try {
                DB::closeConnection('test_sqlite');
                $this->fail('El cierre debe detectar una transacción abierta directamente por PDO');
            } catch (\LogicException $e) {
                $this->assertStringContainsString('active transaction', $e->getMessage());
            }

            try {
                DB::closeAllConnections();
                $this->fail('El cierre global debe detectar una transacción abierta directamente por PDO');
            } catch (\LogicException $e) {
                $this->assertStringContainsString('active transaction', $e->getMessage());
            }

            $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
            $this->assertTrue($connection->inTransaction());
        } finally {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
        }
    }

    public function test_emergency_cleanup_rolls_back_before_closing_connections(): void
    {
        DB::setConnection('test_sqlite');
        $connection = DB::getConnection();
        DB::getConnection('test_sqlite_secondary');
        DB::setConnection('test_sqlite');

        DB::beginTransaction();
        DB::rollbackAndCloseAllConnections();

        $this->assertFalse($connection->inTransaction());
        $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
        $reopenedConnection = DB::getConnection();
        DB::beginTransaction();
        $this->assertTrue($reopenedConnection->inTransaction());
        DB::rollback();
    }

    public function test_response_destructor_uses_emergency_rollback_contract(): void
    {
        DB::setConnection('test_sqlite');
        $connection = DB::getConnection();
        DB::beginTransaction();

        $response = \Boctulus\Simplerest\Core\Response::create();
        unset($response);

        $this->assertFalse($connection->inTransaction());
        $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
        DB::beginTransaction();
        $this->assertTrue(DB::getConnection()->inTransaction());
        DB::rollback();
    }

    public function test_failed_savepoint_release_preserves_outer_transaction(): void
    {
        $connection = DB::getConnection();
        DB::beginTransaction();
        DB::beginTransaction();

        // Fuerza que RELEASE SAVEPOINT falle dentro de DB::commit().
        $connection->exec('RELEASE SAVEPOINT LEVEL_2');
        DB::commit();

        $this->assertTrue($connection->inTransaction(), 'El fallo del savepoint anidado no debe cerrar la transacción exterior');
        DB::commit();
        $this->assertFalse($connection->inTransaction());
    }

    public function test_failed_savepoint_rollback_restores_transaction_context(): void
    {
        $connection = DB::getConnection();
        DB::beginTransaction();
        DB::beginTransaction();

        // Elimina el savepoint para forzar el fallback a rollback completo.
        $connection->exec('RELEASE SAVEPOINT LEVEL_2');
        DB::rollback();

        $this->assertFalse($connection->inTransaction(), 'El fallback debe cerrar la transacción inconsistente');
        DB::beginTransaction();
        $this->assertTrue($connection->inTransaction(), 'Una transacción nueva debe poder comenzar tras el fallback');
        DB::rollback();
    }

    public function test_transaction_exception_restores_connection_context(): void
    {
        $connection = DB::getConnection();

        try {
            DB::transaction(static function (): void {
                throw new \RuntimeException('restore transaction context');
            });
            $this->fail('La excepción del callback debe propagarse');
        } catch (\RuntimeException $e) {
            $this->assertSame('restore transaction context', $e->getMessage());
        }

        $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
        $this->assertFalse($connection->inTransaction());

        DB::setConnection('test_sqlite_secondary');
        $secondaryConnection = DB::getConnection();
        DB::beginTransaction();
        DB::rollback();
        $this->assertFalse($secondaryConnection->inTransaction());
        DB::setConnection('test_sqlite');
    }

    public function test_nested_transaction_inner_commit_keeps_changes()
    {
        DB::beginTransaction();
        try {
            DB::insert("INSERT INTO test_transactions (name, is_test_data) VALUES (?, TRUE)", ['outer_X']);

            DB::beginTransaction(); // inner
            try {
                DB::insert("INSERT INTO test_transactions (name, is_test_data) VALUES (?, TRUE)", ['inner_Y']);
                DB::commit(); // commit inner (release savepoint)
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

            DB::commit(); // commit outer
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        $rowsX = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['outer_X']);
        $rowsY = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['inner_Y']);

        $this->assertNotEmpty($rowsX, "outer_X debe persistir");
        $this->assertNotEmpty($rowsY, "inner_Y debe persistir tras commit anidado");
    }
}
