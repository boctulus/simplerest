<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class StatementCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'statement';
        $this->description = 'Ejecuta una sentencia SQL no-SELECT (INSERT, UPDATE, DELETE...)';
        $this->examples    = [
            'php com sql statement "INSERT INTO logs (msg) VALUES (\'test\')" --connection=main',
            'php com sql statement "DELETE FROM temp WHERE id=1" --connection=main',
            'php com sql statement "DROP TABLE old_table" --connection=main --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['connection'],
            'flags'    => ['force', 'confirm'],
            'options'  => [
                'connection' => ['describe' => 'Nombre de la conexión', 'default' => 'main'],
                'force'      => ['describe' => 'Confirmar operaciones destructivas'],
                'confirm'    => ['describe' => 'Alias de --force'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $query  = $parsed['_positional'][0] ?? null;
        $db     = $this->opt($parsed, 'connection', 'main');
        $force  = $this->opt($parsed, 'force', false) || $this->opt($parsed, 'confirm', false);

        if (!$query) { echo "✗ Se requiere la sentencia SQL como argumento.\n"; return; }
        if (!DB::connectionExists($db)) { echo "✗ Conexión '{$db}' no registrada.\n"; return; }

        $upper = strtoupper(trim($query));
        if (str_starts_with($upper, 'SELECT')) {
            echo "✗ Usa 'php com sql select' para consultas SELECT.\n";
            return;
        }

        $isDestructive = str_starts_with($upper, 'DROP')
            || str_starts_with($upper, 'TRUNCATE')
            || (str_starts_with($upper, 'DELETE') && !preg_match('/\bWHERE\b/i', $query));

        if ($isDestructive && !$force) {
            echo "⚠ Operación destructiva detectada. Agrega --force para ejecutar.\n";
            return;
        }

        DB::setConnection($db);
        $affected = DB::statement($query);
        StdOut::print("Statement executed. Affected rows: {$affected}\r\n");
    }
}
