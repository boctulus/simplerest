<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class SelectCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'select';
        $this->description = 'Ejecuta una consulta SELECT';
        $this->examples    = [
            'php com sql select "SELECT COUNT(*) as total FROM users" --connection=main',
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
        if (!str_starts_with(strtoupper(trim($query)), 'SELECT')) {
            echo "✗ Solo se permiten consultas SELECT.\n";
            return;
        }

        if (!DB::connectionExists($db)) { echo "✗ Conexión '{$db}' no registrada.\n"; return; }
        DB::setConnection($db);

        $results = DB::select($query);
        empty($results)
            ? StdOut::print("Query executed successfully but returned no results\r\n")
            : $this->displayAsTable($results);
    }
}
