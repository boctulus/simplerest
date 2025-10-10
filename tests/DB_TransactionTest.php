<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;

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
        require_once __DIR__ . '/../app.php';

        // Asegúrate de que exista en config.php una conexión 'test_sqlite' parecida a:
        // 'test_sqlite' => ['driver' => 'sqlite', 'db_name' => ':memory:']
        //
        // Si prefieres un archivo temporal, ajusta db_name a un path absoluto a un .sqlite.

        // Seleccionamos la conexión de test
        DB::setConnection('test_sqlite');

        // Creamos tabla de prueba
        DB::statement("CREATE TABLE IF NOT EXISTS test_transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT
        )");
        // limpiar posibles filas previas
        DB::statement("DELETE FROM test_transactions");
    }

    public function tearDown(): void
    {
        // limpiar tras cada test
        DB::statement("DELETE FROM test_transactions");
    }

    public function test_commit_persists()
    {
        DB::transaction(function() {
            DB::insert("INSERT INTO test_transactions (name) VALUES (?)", ['commit_persists']);
        });

        $rows = DB::select("SELECT * FROM test_transactions WHERE name = ?", ['commit_persists']);
        $this->assertNotEmpty($rows, "La fila debe existir después de commit");
    }

    public function test_rollback_on_exception()
    {
        $this->expectException(\Exception::class);

        try {
            DB::transaction(function() {
                DB::insert("INSERT INTO test_transactions (name) VALUES (?)", ['should_rollback']);
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
            DB::insert("INSERT INTO test_transactions (name) VALUES (?)", ['outer_A']);

            // inner
            DB::beginTransaction();
            try {
                DB::insert("INSERT INTO test_transactions (name) VALUES (?)", ['inner_B']);
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

    public function test_nested_transaction_inner_commit_keeps_changes()
    {
        DB::beginTransaction();
        try {
            DB::insert("INSERT INTO test_transactions (name) VALUES (?)", ['outer_X']);

            DB::beginTransaction(); // inner
            try {
                DB::insert("INSERT INTO test_transactions (name) VALUES (?)", ['inner_Y']);
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
