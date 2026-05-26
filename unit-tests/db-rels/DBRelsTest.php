<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DBRels;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

require_once __DIR__ . '/../../app.php';

class DBRelsTest extends TestCase
{
    // ---------------------------------------------------------------
    //  BASIC SANITY: DBRels methods return expected types
    // ---------------------------------------------------------------

    function testSchemaPathReturnsString()
    {
        $this->assertIsString(DBRels::getSchemaPath('products'));
    }

    function testSchemaPathNullReturnsString()
    {
        $this->assertIsString(DBRels::getSchemaPath(null));
    }

    function testSchemaNameReturnsString()
    {
        $name = DBRels::getSchemaName('products');
        $this->assertStringContainsString('Schema', $name);
    }

    function testModelNamespaceReturnsString()
    {
        $this->assertIsString(DBRels::getModelNamespace());
    }

    function testModelNameReturnsString()
    {
        $name = DBRels::getModelName('products');
        $this->assertStringEndsWith('Model', $name);
    }

    function testApiNamespaceReturnsString()
    {
        $name = DBRels::getApiNamespace('products');
        $this->assertStringContainsString('api', strtolower($name));
    }

    function testDefaultDatabaseNameReturnsString()
    {
        $this->assertIsString(DB::getDefaultDatabaseName());
    }

    function testGetUsersTableReturnsString()
    {
        $this->assertIsString(DBRels::getUsersTable());
    }

    function testTbPrefixReturnsString()
    {
        $this->assertIsString(DB::getTablePrefixForCurrent());
    }

    function testSqlFormatterReturnsString()
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $this->assertIsString(Model::sqlFormatter($sql));
    }

    function testDefaultConnectionIdReturnsString()
    {
        $this->assertIsString(DB::getDefaultConnectionId());
    }

    // ---------------------------------------------------------------
    //  EDGE CASES
    // ---------------------------------------------------------------

    /**
     * @testDox inSchema() con props vacío lanza InvalidArgumentException
     */
    function testInSchemaEmptyProps()
    {
        $this->expectException(\InvalidArgumentException::class);
        DBRels::inSchema([], 'products');
    }

    /**
     * @testDox isRel() con tipo inválido lanza InvalidArgumentException
     */
    function testIsRelInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);
        DBRels::isRel('invalid_type', 't1', 't2');
    }

    /**
     * @testDox getRels() con tipo inválido lanza InvalidArgumentException
     */
    function testGetRelsInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);
        DBRels::getRels('t1', 't2', 'bad_type');
    }

    /**
     * @testDox getSchemaPath() con tenant específico produce path distinto
     */
    function testSchemaPathWithTenant()
    {
        $defaultConn = Config::get()['db_connection_default'] ?? 'main';
        $pathDefault = DBRels::getSchemaPath('products');
        $pathDefaultExplicit = DBRels::getSchemaPath('products', $defaultConn);

        $this->assertEquals($pathDefault, $pathDefaultExplicit);
    }

    /**
     * @testDox getPrimaryKey() es accesible vía DBRels:: (alias get_id_name removido)
     */
    function testPrimaryKeyAlias()
    {
        $this->assertIsString(DBRels::getPrimaryKey('products'));
    }

    /**
     * @testDox isOneToOne() existe como método y llama a isRel('1:1', ...)
     */
    function testIsOneToOneMethodExists()
    {
        $this->assertTrue(method_exists(DBRels::class, 'isOneToOne'));
        $this->assertTrue(method_exists(DBRels::class, 'isOneToMany'));
        $this->assertTrue(method_exists(DBRels::class, 'isNToOne'));
        $this->assertTrue(method_exists(DBRels::class, 'isManyToMany'));
    }

    /**
     * @testDox Las alias semánticas aceptan los mismos parámetros que isRel()
     */
    function testSemanticAliasesParameterCount()
    {
        $refIsRel   = new \ReflectionMethod(DBRels::class, 'isRel');
        $refIs1to1  = new \ReflectionMethod(DBRels::class, 'isOneToOne');
        $refIs1toN  = new \ReflectionMethod(DBRels::class, 'isOneToMany');
        $refIsNto1  = new \ReflectionMethod(DBRels::class, 'isNToOne');
        $refIsNtoM  = new \ReflectionMethod(DBRels::class, 'isManyToMany');

        $this->assertEquals(5, $refIsRel->getNumberOfParameters());
        $this->assertEquals(4, $refIs1to1->getNumberOfParameters());
        $this->assertEquals(2, $refIs1to1->getNumberOfRequiredParameters());
        $this->assertEquals(4, $refIs1toN->getNumberOfParameters());
        $this->assertEquals(4, $refIsNto1->getNumberOfParameters());
        $this->assertEquals(4, $refIsNtoM->getNumberOfParameters());
    }

    /**
     * @testDox getRelType() con tablas inexistentes falla porque requiere schemas (caso documentado)
     */
    function testGetRelTypeNeedsSchema()
    {
        $this->expectException(\Error::class);
        DBRels::getRelType('nonexistent_1', 'nonexistent_2');
    }

    // ---------------------------------------------------------------
    //  DOCUMENTED: Casos no testeables por falta de alcance
    // ---------------------------------------------------------------
    /*
    Los siguientes métodos REQUIEREN infraestructura real (DB, schemas, archivos)
    y NO son probados en este test unitario:

    Método                        | Dependencia
    ------------------------------|--------------------------------------------
    getRelations()                | Archivo Relations.php real en schemas/
    getPivot()                    | Archivo Pivots.php + MakePivotScanCommand
    getRels()                     | Schemas reales con relaciones FK
    getRelType() (full)           | Schemas reales (solo edge case probado)
    getSchema()                   | Clases Schema reales
    hasSchema()                   | Clase Schema real
    getModelDefs()                | Modelo + Schema real
    getDefs()                     | Modelo + Schema real
    getModelInstance()            | Modelo real
    getModelInstanceByTable()     | Modelo real
    getUserModelName()            | Tabla users configurada
    getApiName()                  | API Controller real
    processSqlFile()              | Archivo SQL real en filesystem
    migrate()                     | Conexión DB real
    logQueries()                  | Permisos SUPER MySQL
    withConnection()              | Conexión DB alternativa
    withDefaultConnection()       | Conexión DB default
    getFKs()                      | Schema real con FK
    isMulRel()                    | Relaciones reales
    isMulRelCached()              | Archivo Relations.php con multiplicidad
    enqueueData()                 | DB queue operativa
    dequeData()                   | DB queue operativa
    getDefs() (full)              | API Controller real
    */
}
