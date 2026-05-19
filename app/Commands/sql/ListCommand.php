<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class SqlListCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'list';
        $this->description = "Lista tablas de una BD o registros de '{db}.{table}'";
        $this->aliases     = ['ls'];
        $this->examples    = [
            "php com sql list 'main'",
            "php com sql list 'main.users'",
            "php com sql list 'main.users' --limit=20 --format=table",
            "php com sql list 'main.users' --skip=10 --limit=5",
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['limit', 'take', 'skip', 'offset', 'format'],
            'flags'    => [],
            'options'  => [
                'limit'  => ['describe' => 'Máximo de registros', 'default' => 10],
                'take'   => ['describe' => 'Alias de --limit'],
                'skip'   => ['describe' => 'Registros a saltar', 'default' => 0],
                'offset' => ['describe' => 'Alias de --skip'],
                'format' => ['describe' => 'Formato de salida: simple, table', 'default' => 'simple'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $tableArg = $parsed['_positional'][0] ?? null;
        if (!$tableArg) {
            echo "✗ Se requiere '{db}' o '{db}.{table}' como argumento.\n";
            $this->showUsage();
            return;
        }

        if (!Strings::contains('.', $tableArg)) {
            // List tables in DB
            $db = $tableArg;
            if (!DB::connectionExists($db)) {
                echo "✗ Conexión '{$db}' no registrada.\n";
                return;
            }
            DB::setConnection($db);
            $tables = DB::select("SHOW TABLES");
            if (empty($tables)) { echo "No hay tablas en '{$db}'.\n"; return; }
            echo "Tablas en '{$db}':\n";
            foreach ($tables as $row) echo '  - ' . reset($row) . "\n";
            return;
        }

        $conn = $this->resolveConnection($tableArg);
        if (!$conn) return;

        $limit  = (int) ($this->opt($parsed, 'limit') ?? $this->opt($parsed, 'take', 10));
        $skip   = (int) ($this->opt($parsed, 'skip')  ?? $this->opt($parsed, 'offset', 0));
        $format = $this->opt($parsed, 'format', 'simple');

        $query = table($conn['table']);
        if ($limit > 0) $query->limit($limit);
        if ($skip  > 0) $query->offset($skip);
        $results = $query->get();

        if (empty($results)) { StdOut::print("No records found\r\n"); return; }
        $format === 'table' ? $this->displayAsTable($results) : $this->displaySimple($results);
    }
}
