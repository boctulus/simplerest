<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class QueryCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'query';
        $this->description = 'Ejecuta una consulta SQL general (SELECT, SHOW, DESCRIBE)';
        $this->examples    = [
            'php com sql query "SELECT COUNT(*) FROM users" --connection=main',
            'php com sql query "SHOW TABLES" --connection=main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['connection'],
            'flags'    => [],
            'options'  => [
                'connection' => ['describe' => 'Nombre de la conexión', 'default' => 'main'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $query = $parsed['_positional'][0] ?? null;
        $db    = $this->opt($parsed, 'connection', 'main');

        if (!$query) { echo "✗ Se requiere la consulta SQL como argumento.\n"; return; }
        if (!DB::connectionExists($db)) { echo "✗ Conexión '{$db}' no registrada.\n"; return; }

        $upper = strtoupper(trim($query));
        $isRead = str_starts_with($upper, 'SELECT') || str_starts_with($upper, 'DESCRIBE') || str_starts_with($upper, 'SHOW');

        DB::setConnection($db);

        if ($isRead) {
            $results = DB::select($query);
            empty($results)
                ? StdOut::print("No results\r\n")
                : $this->displayAsTable($results);
        } else {
            $affected = DB::statement($query);
            StdOut::print("Query executed. Affected rows: {$affected}\r\n");
        }
    }
}
