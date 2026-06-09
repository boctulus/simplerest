<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\Strings;

class SchemaBugsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../app.php';
    }

    /*
        Bug #3: primary() nullable bug
        When primary() is called on a field that also has nullable(),
        the generated SQL must force NOT NULL (PRIMARY KEY implies NOT NULL)
    */
    public function test_primary_key_forces_not_null_in_create(): void
    {
        $schema = new Schema('_test_primary_nullable');
        $schema->dontExec();
        $schema->int('id')->nullable()->primary();
        $schema->varchar('name');
        $schema->create();

        $sql = $schema->dd();

        $this->assertDoesNotMatchRegularExpression('/(?<!NOT )NULL\s+PRIMARY KEY/', $sql,
            'SQL must not contain contradictory NULL PRIMARY KEY (without NOT before NULL)');
        $this->assertMatchesRegularExpression('/`id` INT[^,]+NOT NULL/', $sql,
            'PRIMARY KEY field must be NOT NULL');
        $this->assertMatchesRegularExpression('/`id` INT[^,]+PRIMARY KEY/', $sql,
            'PRIMARY KEY must be present in field definition');
        $this->assertStringContainsString('PRIMARY KEY', $sql,
            'PRIMARY KEY must be present in field definition');
    }

    public function test_primary_without_nullable_works(): void
    {
        $schema = new Schema('_test_primary_basic');
        $schema->dontExec();
        $schema->int('id')->primary();
        $schema->varchar('name');
        $schema->create();

        $sql = $schema->dd();

        $this->assertStringContainsString('NOT NULL', $sql);
        $this->assertStringContainsString('PRIMARY KEY', $sql);
    }

    public function test_auto_increment_primary_still_works(): void
    {
        $schema = new Schema('_test_auto_primary');
        $schema->dontExec();
        $schema->increments('id');
        $schema->varchar('name');
        $schema->create();

        $sql = $schema->dd();

        $this->assertStringContainsString('AUTO_INCREMENT', $sql);
        $this->assertStringContainsString('NOT NULL', $sql);
    }

    /*
        Bug #4: addUnique() multi-campo
        addUnique() debe generar SQL correcto tanto para
        una columna como para múltiples columnas
    */
    public function test_addUnique_with_single_column(): void
    {
        $schema = new Schema('_test_unique_single');
        $schema->addUnique('email');

        $ref  = new \ReflectionProperty(Schema::class, 'commands');
        $ref->setAccessible(true);
        $commands = $ref->getValue($schema);

        $this->assertGreaterThan(0, count($commands));
        $this->assertStringContainsString('ADD UNIQUE(`email`)', $commands[0]);
    }

    public function test_addUnique_with_multiple_columns(): void
    {
        $schema = new Schema('_test_unique_multi');
        $schema->addUnique(['email', 'name']);

        $ref  = new \ReflectionProperty(Schema::class, 'commands');
        $ref->setAccessible(true);
        $commands = $ref->getValue($schema);

        $this->assertGreaterThan(0, count($commands));
        $this->assertStringContainsString('ADD UNIQUE(`email`,`name`)', $commands[0]);
    }

    public function test_addUnique_then_create_includes_unique(): void
    {
        $schema = new Schema('_test_unique_create');
        $schema->dontExec();
        $schema->increments('id');
        $schema->varchar('email');
        $schema->varchar('name');
        $schema->unique();  // marca field actual ('name') como UNIQUE via indices
        $schema->create();

        $sql = $schema->dd();

        $this->assertStringContainsString('ADD UNIQUE KEY', $sql,
            'create() debe incluir unique definido via unique()');
    }

    /*
        Verifica que Strings::backticks() con array funcione correctamente
    */
    public function test_strings_backticks_with_array(): void
    {
        $result = Strings::backticks(['email', 'name']);
        $this->assertIsArray($result);
        $this->assertEquals('`email`', $result[0]);
        $this->assertEquals('`name`', $result[1]);

        $imploded = implode(',', $result);
        $this->assertEquals('`email`,`name`', $imploded);
    }
}
