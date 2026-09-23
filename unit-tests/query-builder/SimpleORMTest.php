<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

/*
 * Test simplificado del ORM Layer
 */

class SimpleORMTest extends TestCase
{
    private static ?string $previousConnection = null;

    public static function setUpBeforeClass(): void
    {
        self::$previousConnection = DB::getCurrentConnectionId();
        DB::setConnection('test_sqlite');
        DB::statement('CREATE TABLE IF NOT EXISTS orm_items (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, note TEXT)');
        DB::statement('CREATE TABLE IF NOT EXISTS orm_custom_key (code TEXT PRIMARY KEY, name TEXT)');
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$previousConnection === null) {
            DB::closeConnection();
        } else {
            DB::setConnection(self::$previousConnection);
        }
    }

    protected function setUp(): void
    {
        DB::setConnection('test_sqlite');
        DB::statement('DELETE FROM orm_items');
        DB::statement('DELETE FROM orm_custom_key');
    }

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

    public function test_create_read_update_and_delete_through_query_builder(): void
    {
        $record = ORMItem::newInstance(['name' => 'first', 'note' => 'original']);
        $this->assertTrue($record->save());
        $this->assertTrue($record->exists());
        $this->assertNotNull($record->id);

        $query = new ORMItem();
        $found = $query->findRecord($record->id);
        $this->assertSame('first', $found->name);
        $this->assertCount(1, $query->where(['name' => 'first'])->getRecords());
        $this->assertIsArray((new ORMItem())->find($record->id)->first());

        $found->name = 'updated';
        $this->assertTrue($found->save());
        $this->assertSame('updated', (new ORMItem())->find($record->id)->first()['name']);
        $this->assertTrue($found->save()); // unchanged entity

        $this->assertTrue($found->delete());
        $this->assertFalse($found->exists());
        $this->assertNull((new ORMItem())->findRecord($record->id));
    }

    public function test_record_update_only_changes_its_own_row(): void
    {
        $a = ORMItem::newInstance(['name' => 'a']);
        $b = ORMItem::newInstance(['name' => 'b']);
        $a->save();
        $b->save();
        $a->name = 'changed';
        $a->save();
        $this->assertSame('b', (new ORMItem())->findRecord($b->id)->name);
    }

    public function test_entity_hydration_respects_query_builder_order_and_limit(): void
    {
        ORMItem::newInstance(['name' => 'z'])->save();
        ORMItem::newInstance(['name' => 'a'])->save();
        $records = (new ORMItem())->orderBy(['name' => 'ASC'])->limit(1)->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame('a', $records[0]->name);
    }

    public function test_schema_primary_key_is_used_for_persistence(): void
    {
        $record = ORMCustomKey::newInstance(['code' => 'alpha', 'name' => 'before']);
        $this->assertTrue($record->save());
        $found = (new ORMCustomKey())->findRecord('alpha');
        $this->assertSame('before', $found->name);
        $found->name = 'after';
        $this->assertTrue($found->save());
        $this->assertSame('after', (new ORMCustomKey())->findRecord('alpha')->name);
        $this->assertTrue($found->delete());
        $this->assertNull((new ORMCustomKey())->findRecord('alpha'));
    }
}

class ORMItem extends Model
{
    public function __construct(bool $connect = false)
    {
        parent::__construct(false, null, false);
        $this->table('orm_items');
        $this->setConn(DB::getConnection('test_sqlite'));
    }
}

class ORMCustomKey extends Model
{
    public function __construct(bool $connect = false)
    {
        parent::__construct(false, ORMCustomKeySchema::class, false);
        $this->setConn(DB::getConnection('test_sqlite'));
    }
}

class ORMCustomKeySchema
{
    public static function get(): array
    {
        return [
            'table_name' => 'orm_custom_key',
            'id_name' => 'code',
            'attr_types' => ['code' => 'STR', 'name' => 'STR'],
            'nullable' => [],
            'rules' => [],
        ];
    }
}
