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
use Boctulus\Simplerest\Core\Interfaces\IValidator;
use Boctulus\Simplerest\Core\Exceptions\InvalidValidationException;

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
        DB::statement('CREATE TABLE IF NOT EXISTS orm_custom_key (code TEXT PRIMARY KEY, name TEXT, deleted_at TEXT)');
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

    public function test_record_hydration_uses_arrays_without_changing_builder_fetch_mode(): void
    {
        ORMItem::newInstance(['name' => 'first'])->save();
        $query = (new ORMItem())->asObject()->where(['name' => 'first']);

        $this->assertSame('first', $query->firstRecord()->name);
        $this->assertSame('first', $query->getRecords()[0]->name);
        $this->assertIsObject($query->first());
        $this->assertIsObject($query->get()[0]);

        $query->column();
        $this->assertSame('first', $query->getRecords()[0]->name);
        $this->assertIsScalar($query->get()[0]);
    }

    public function test_record_write_keeps_runtime_mutator_and_discards_read_filters(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', 'before')");
        $query = (new ORMCustomKey())
            ->registerInputMutator('name', fn ($name) => strtoupper($name), null)
            ->where(['name' => 'before'])
            ->orderBy(['name' => 'ASC'])
            ->limit(1);

        $record = $query->firstRecord();
        DB::statement("UPDATE orm_custom_key SET name = 'external' WHERE code = 'alpha'");
        $record->name = 'later';

        $this->assertTrue($record->save());
        $this->assertSame('LATER', (new ORMCustomKey())->findRecord('alpha')->name);
    }

    public function test_record_write_keeps_runtime_fillable_configuration(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', 'before')");
        $query = (new ORMRestrictedKey())->fill(['name']);
        $record = $query->findRecord('alpha');
        $record->name = 'after';

        $this->assertTrue($record->save());
        $this->assertSame('after', (new ORMCustomKey())->findRecord('alpha')->name);
    }

    public function test_record_write_keeps_runtime_validator(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', 'before')");
        $query = (new ORMCustomKey())->where(['code' => 'alpha']);
        $record = $query->firstRecord();
        $query->setValidator(new ORMRejectValidator());
        $record->name = 'after';

        $this->expectException(InvalidValidationException::class);
        $record->save();
    }

    public function test_record_delete_keeps_runtime_soft_delete_setting(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', 'before')");
        $record = (new ORMCustomKey())->setSoftDelete(false)->findRecord('alpha');
        $this->assertTrue($record->delete());
        $this->assertSame('0', (string) DB::select("SELECT COUNT(*) AS n FROM orm_custom_key WHERE code = 'alpha'")[0]['n']);
    }

    public function test_zero_updated_rows_distinguishes_missing_record(): void
    {
        $record = ORMItem::newInstance(['name' => 'before']);
        $record->save();
        $loaded = (new ORMItem())->findRecord($record->id);
        (new ORMItem())->find($record->id)->delete();
        $loaded->name = 'after';

        $this->assertFalse($loaded->save());
        $this->assertFalse($loaded->exists());
    }

    public function test_zero_updated_rows_can_leave_an_existing_record(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', 'before')");
        $record = (new ORMZeroAffectedKey())->findRecord('alpha');
        $record->name = 'after';
        DB::statement("UPDATE orm_custom_key SET name = 'after' WHERE code = 'alpha'");

        $this->assertTrue($record->save());
        $this->assertTrue($record->exists());
    }

    public function test_zero_updated_rows_do_not_confirm_unstored_changes(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', 'before')");
        $record = (new ORMZeroAffectedKey())->findRecord('alpha');
        $record->name = 'after';

        $this->assertFalse($record->save());
        $this->assertTrue($record->exists());
        $this->assertSame('before', (new ORMCustomKey())->findRecord('alpha')->name);
    }

    public function test_zero_updated_rows_compare_text_without_numeric_coercion(): void
    {
        DB::statement("INSERT INTO orm_custom_key (code, name) VALUES ('alpha', '0e1')");
        $record = (new ORMZeroAffectedKey())->findRecord('alpha');
        $record->name = '0e2';

        $this->assertFalse($record->save());
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

class ORMRestrictedKey extends ORMCustomKey
{
    protected $not_fillable = ['name'];
}

class ORMZeroAffectedKey extends ORMCustomKey
{
    public function update(array $data, $set_updated_at = true)
    {
        return 0;
    }
}

class ORMRejectValidator implements IValidator
{
    public function validate(array $data, array $rules, $fillables = null, $not_fillables = null)
    {
        return false;
    }

    public function getErrors(): array
    {
        return ['name' => ['rejected']];
    }
}

class ORMCustomKeySchema
{
    public static function get(): array
    {
        return [
            'table_name' => 'orm_custom_key',
            'id_name' => 'code',
            'attr_types' => ['code' => 'STR', 'name' => 'STR', 'deleted_at' => 'STR'],
            'nullable' => ['deleted_at'],
            'rules' => [],
        ];
    }
}
