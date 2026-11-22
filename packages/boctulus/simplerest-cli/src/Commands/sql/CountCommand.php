<?php

use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class CountCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'count';
        $this->description = "Cuenta registros en '{db}.{table}'";
        $this->examples    = ["php com sql count 'main.users'"];
    }

    public function execute(array $parsed): void
    {
        $tableArg = $parsed['_positional'][0] ?? null;
        if (!$tableArg) { echo "✗ Se requiere '{db}.{table}'.\n"; return; }

        $conn = $this->resolveConnection($tableArg);
        if (!$conn) return;

        $count = table($conn['table'])->count();
        StdOut::print("Table '{$conn['table']}' has {$count} record(s) in connection '{$conn['db']}'\r\n");
    }
}
