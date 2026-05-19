<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class ListDatabasesCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'list-databases';
        $this->description = 'Lista todas las bases de datos del servidor';
        $this->aliases     = ['databases', 'dbs'];
        $this->examples    = [
            'php com sql list-databases',
            'php com sql list-databases --connection=main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['connection'],
            'flags'    => [],
            'options'  => ['connection' => ['describe' => 'Nombre de la conexión', 'default' => 'main']],
        ];
    }

    public function execute(array $parsed): void
    {
        $db     = $this->opt($parsed, 'connection', 'main');
        $config = Config::get();
        $driver = $config['db_connections'][$db]['driver'] ?? 'mysql';

        if (!DB::connectionExists($db)) { echo "✗ Conexión '{$db}' no registrada.\n"; return; }
        DB::setConnection($db);

        $sql = match (true) {
            in_array($driver, ['sqlsrv', 'mssql']) => "SELECT name FROM sys.databases ORDER BY name",
            in_array($driver, ['pgsql', 'postgres']) => "SELECT datname FROM pg_database ORDER BY datname",
            default => "SHOW DATABASES",
        };

        $databases = DB::select($sql);
        if (empty($databases)) { StdOut::print("No databases found\r\n"); return; }

        StdOut::print("Databases on '{$db}' ({$driver}):\r\n" . str_repeat('-', 40) . "\r\n");
        foreach ($databases as $row) StdOut::print("  - " . reset($row) . "\r\n");
        StdOut::print(str_repeat('-', 40) . "\r\n");
        StdOut::print("Total: " . count($databases) . " database(s)\r\n");
    }
}
