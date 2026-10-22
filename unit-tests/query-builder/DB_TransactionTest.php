<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;

/*
    * Asegúrate de tener una conexión SQLite en config.php para pruebas.
    * Ejemplo:
    * 'test_sqlite' => ['driver' => 'sqlite', 'db_name' => ':memory:']
    *
    * O usa un archivo temporal si prefieres persistencia entre pruebas.
    *
    * Estos tests verifican que las transacciones funcionan correctamente,
    * incluyendo commits, rollbacks y transacciones anidadas con savepoints.
    *
    * Requiere PHPUnit y una configuración adecuada de la base de datos.
    *
    * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/DB_TransactionTest.php    
    *
*/
class DB_TransactionTest extends TestCase
{
    protected static $tmpDbFile;

    public static function setUpBeforeClass(): void
    {
        // Cargar la aplicación (incluye constantes y configuración)
        require_once __DIR__ . '/../../app.php';

        $testConfig = Config::get('db_connections.test_sqlite');
        if (($testConfig['driver'] ?? null) !== 'sqlite' || ($testConfig['db_name'] ?? null) !== ':memory:') {
            throw new \RuntimeException('DB transaction tests require the isolated SQLite :memory: alias.');
        }

        Config::set('db_connections.test_sqlite_secondary', [
            'driver' => 'sqlite',
            'db_name' => ':memory:',
            'host' => null,
            'user' => null,
            'pass' => null,
            'pdo_options' => null,
        ]);

        // Seleccionamos la conexión de test
        DB::setConnection('test_sqlite');

        $connection = DB::getConnection();
        if ($connection->getAttribute(\PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new \RuntimeException('DB transaction tests did not connect through SQLite.');
        }

        // Creamos tabla de prueba
        $connection->exec("CREATE TABLE IF NOT EXISTS test_transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            is_test_data BOOLEAN NOT NULL DEFAULT TRUE
        )");
        // limpiar posibles filas previas
        $connection->exec("DELETE FROM test_transactions WHERE is_test_data = TRUE");
    }

    public static function tearDownAfterClass(): void
    {
        DB::closeAllConnections();
    }

    public function tearDown(): void
    {
        // limpiar tras cada test
        DB::setConnection('test_sqlite');
        DB::statement("DELETE FROM test_transactions WHERE is_test_data = TRUE");
    }

    private function transactionLevel(?string $connectionId = null): int
    {
        $property = (new \ReflectionClass(DB::class))->getProperty('transactionLevels');
        $property->setAccessible(true);
        $levels = $property->getValue();
        $connectionId ??= DB::getCurrentConnectionId();

        return (int) ($levels[$connectionId] ?? 0);
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
            $this->assertSame(0, $this->transactionLevel(), 'El nivel debe quedar intacto tras un BEGIN fallido');
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
            $this->assertSame(1, $this->transactionLevel(), 'La siguiente transacción debe comenzar como nivel 1');
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
            $this->assertSame(1, $this->transactionLevel('test_sqlite'));
            $this->assertSame(0, $this->transactionLevel('test_sqlite_secondary'));

            try {
                DB::setConnection('test_sqlite_secondary');
                $this->fail('Debe rechazarse el cambio de conexión durante una transacción activa');
            } catch (\LogicException $e) {
                $this->assertStringContainsString('active transaction', $e->getMessage());
            }

            $this->assertSame('test_sqlite', DB::getCurrentConnectionId());
            $this->assertTrue($primaryConnection->inTransaction());
            DB::commit();
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
            $this->assertSame(0, $this->transactionLevel('test_sqlite'));
            $this->assertSame(1, $this->transactionLevel('test_sqlite_secondary'));

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
