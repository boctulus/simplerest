<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Model;

/**
 * @group refactor
 */
/*
 * Test simplificado del ORM Layer
 */

class SimpleORMTest extends TestCase
{
    public function test_newInstance()
    {
        $instance = Model::newInstance(['name' => 'test'], false);

        $this->assertEquals('test', $instance->name);
        $this->assertFalse($instance->exists());
    }

    public function test_newInstance_existing()
    {
        $instance = Model::newInstance(['id' => 1, 'name' => 'test'], true);

        $this->assertEquals(1, $instance->id);
        $this->assertEquals('test', $instance->name);
        $this->assertTrue($instance->exists());
    }
}
