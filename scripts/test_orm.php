<?php declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\TemporaryExceptionHandler;
use Boctulus\Simplerest\Core\Model;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (PHP_SAPI !== 'cli') {
    return;
}

require_once __DIR__ . '/../app.php';

// Run with: php scripts/test_orm.php
// The configured SQLite database must be in memory; no application rows are touched.
class OrmDemoUserSchema
{
    public static function get(): array
    {
        return [
            'table_name' => 'orm_demo_users',
            'id_name' => 'id',
            'attr_types' => ['id' => 'INT', 'name' => 'STR', 'active' => 'INT'],
            'nullable' => ['id'],
            'rules' => [],
        ];
    }
}

class OrmDemoUser extends Model
{
    public function __construct(bool $connect = false)
    {
        parent::__construct(false, OrmDemoUserSchema::class, false);
        $this->setConn(DB::getConnection('test_sqlite'));
    }
}

function ormDemoCheck(bool $passed, string $step): void
{
    if (!$passed) {
        throw new RuntimeException("ORM demo failed: {$step}");
    }

    echo "[OK] {$step}", PHP_EOL;
}

$handler = new TemporaryExceptionHandler();

try {
    $sqlite = Config::get()['db_connections']['test_sqlite'] ?? null;
    if (($sqlite['driver'] ?? null) !== 'sqlite' || ($sqlite['db_name'] ?? null) !== ':memory:') {
        throw new RuntimeException('test_sqlite must be configured as an in-memory SQLite database');
    }

    DB::setConnection('test_sqlite');
    DB::statement('CREATE TABLE orm_demo_users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, active INTEGER NOT NULL)');

    $ana = OrmDemoUser::newInstance(['name' => 'Ana', 'active' => 1]);
    ormDemoCheck(!$ana->exists() && $ana->save() && $ana->exists(), 'create a model record');

    $builder = new OrmDemoUser(true);
    $row = $builder->find($ana->id)->first();
    ormDemoCheck(is_array($row) && $row['name'] === 'Ana', 'Query Builder still returns an array');

    $record = (new OrmDemoUser(true))->findRecord($ana->id);
    ormDemoCheck($record !== null && $record->exists() && $record->name === 'Ana', 'hydrate a model record');

    OrmDemoUser::newInstance(['name' => 'Bea', 'active' => 1])->save();
    $records = (new OrmDemoUser(true))
        ->where(['active' => 1])
        ->orderBy(['name' => 'ASC'])
        ->limit(1)
        ->getRecords();
    ormDemoCheck(count($records) === 1 && $records[0]->name === 'Ana', 'filter, order and limit through Query Builder');

    $record->name = 'Ana María';
    ormDemoCheck($record->save() && (new OrmDemoUser(true))->findRecord($ana->id)->name === 'Ana María', 'update the model record');

    ormDemoCheck($record->delete() && !$record->exists() && (new OrmDemoUser(true))->findRecord($ana->id) === null, 'delete the model record');

    echo 'ORM demo completed successfully.', PHP_EOL;
} catch (Throwable $e) {
    $handler->exception_handler($e);
    exit(1);
}
