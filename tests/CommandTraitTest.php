<?php

namespace Boctulus\Simplerest\Tests;

use PHPUnit\Framework\TestCase;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../app.php';

use Boctulus\Simplerest\Core\Traits\CommandTrait;

/**
 * @group refactor
 * Test para CommandTrait
 *
 * Prueba las funcionalidades de parseo de opciones de línea de comandos
 *
 * Ejecutar con: ./vendor/bin/phpunit tests/CommandTraitTest.php
 */
class CommandTraitTest extends TestCase
{
    private $command;

    protected function setUp(): void
    {
        // Crear una clase anónima que use el trait
        $this->command = new class {
            use CommandTrait;

            // Hacer público el método para testing
            public function parseOptionsPublic(array $args): array
            {
                return $this->parseOptions($args);
            }

            public function getOptionPublic(array $options, string $key, $default = null)
            {
                return $this->getOption($options, $key, $default);
            }
        };
    }

    /**
     * Test parseOptions con formato --key=value
     */
    public function testParseOptionsWithEqualSign()
    {
        $args = ['--limit=100', '--offset=50'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('100', $result['limit']);
        $this->assertEquals('50', $result['offset']);
    }

    /**
     * Test parseOptions con formato --key:value
     */
    public function testParseOptionsWithColon()
    {
        $args = ['--name:John', '--age:30'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('John', $result['name']);
        $this->assertEquals('30', $result['age']);
    }

    /**
     * Test parseOptions con flags booleanos
     */
    public function testParseOptionsWithBooleanFlags()
    {
        $args = ['--dry-run', '--verbose', '--force'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertTrue($result['dry_run']);
        $this->assertTrue($result['verbose']);
        $this->assertTrue($result['force']);
    }

    /**
     * Test parseOptions con valores entre comillas dobles
     */
    public function testParseOptionsWithDoubleQuotes()
    {
        $args = ['--name="John Doe"', '--message="Hello World"'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('John Doe', $result['name']);
        $this->assertEquals('Hello World', $result['message']);
    }

    /**
     * Test parseOptions con valores entre comillas simples
     */
    public function testParseOptionsWithSingleQuotes()
    {
        $args = ["--name='Jane Doe'", "--city='New York'"];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('Jane Doe', $result['name']);
        $this->assertEquals('New York', $result['city']);
    }

    /**
     * Test parseOptions convierte guiones a guiones bajos
     */
    public function testParseOptionsConvertsHyphensToUnderscores()
    {
        $args = ['--dry-run', '--keep-module', '--only-unmapped'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertTrue($result['dry_run']);
        $this->assertTrue($result['keep_module']);
        $this->assertTrue($result['only_unmapped']);
    }

    /**
     * Test parseOptions con mezcla de formatos
     */
    public function testParseOptionsWithMixedFormats()
    {
        $args = [
            '--limit=100',
            '--dry-run',
            '--author:"Pablo Bozzolo"',
            '--verbose'
        ];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('100', $result['limit']);
        $this->assertTrue($result['dry_run']);
        $this->assertEquals('Pablo Bozzolo', $result['author']);
        $this->assertTrue($result['verbose']);
    }

    /**
     * Test parseOptions con array vacío
     */
    public function testParseOptionsWithEmptyArray()
    {
        $args = [];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test parseOptions ignora argumentos sin formato válido
     */
    public function testParseOptionsIgnoresInvalidArguments()
    {
        $args = ['invalid', 'also-invalid', '--valid=true'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertArrayNotHasKey('invalid', $result);
        $this->assertArrayNotHasKey('also-invalid', $result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertEquals('true', $result['valid']);
    }

    /**
     * Test getOption retorna valor existente
     */
    public function testGetOptionReturnsExistingValue()
    {
        $options = ['limit' => '100', 'dry_run' => true];

        $this->assertEquals('100', $this->command->getOptionPublic($options, 'limit'));
        $this->assertTrue($this->command->getOptionPublic($options, 'dry_run'));
    }

    /**
     * Test getOption retorna valor por defecto
     */
    public function testGetOptionReturnsDefaultValue()
    {
        $options = ['limit' => '100'];

        $this->assertEquals(50, $this->command->getOptionPublic($options, 'offset', 50));
        $this->assertNull($this->command->getOptionPublic($options, 'missing'));
    }

    /**
     * Test getOption con clave inexistente sin default
     */
    public function testGetOptionWithMissingKeyNoDefault()
    {
        $options = ['limit' => '100'];

        $this->assertNull($this->command->getOptionPublic($options, 'missing'));
    }

    /**
     * Test caso real: comando zippy process_categories
     */
    public function testRealWorldZippyCommand()
    {
        $args = ['--limit=100', '--dry-run', '--strategy=llm'];
        $result = $this->command->parseOptionsPublic($args);

        $limit = $this->command->getOptionPublic($result, 'limit', 100);
        $dryRun = $this->command->getOptionPublic($result, 'dry_run', false);
        $strategy = $this->command->getOptionPublic($result, 'strategy', null);

        $this->assertEquals('100', $limit);
        $this->assertTrue($dryRun);
        $this->assertEquals('llm', $strategy);
    }

    /**
     * Test caso real: comando file list
     */
    public function testRealWorldFileCommand()
    {
        $args = ['--pattern=*.php', '--recursive', '--exclude=/vendor/'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('*.php', $result['pattern']);
        $this->assertTrue($result['recursive']);
        $this->assertEquals('/vendor/', $result['exclude']);
    }

    /**
     * Test caso real: comando module toPackage
     */
    public function testRealWorldModuleCommand()
    {
        $args = ['--author=Pablo Bozzolo', '--keep-module'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('Pablo Bozzolo', $result['author']);
        $this->assertTrue($result['keep_module']);
    }

    /**
     * Test parseOptions con caracteres especiales
     */
    public function testParseOptionsWithSpecialCharacters()
    {
        $args = ['--slug=dairy.milk', '--category="Aceites Y Condimentos"'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('dairy.milk', $result['slug']);
        $this->assertEquals('Aceites Y Condimentos', $result['category']);
    }

    /**
     * Test parseOptions con números
     */
    public function testParseOptionsWithNumbers()
    {
        $args = ['--port=8080', '--timeout=3000', '--retries=5'];
        $result = $this->command->parseOptionsPublic($args);

        $this->assertEquals('8080', $result['port']);
        $this->assertEquals('3000', $result['timeout']);
        $this->assertEquals('5', $result['retries']);
    }
}
