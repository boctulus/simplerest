<?php

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class DescribeCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'describe';
        $this->description = "Muestra la estructura de '{db}.{table}'";
        $this->aliases     = ['desc'];
        $this->examples    = ["php com sql describe 'main.users'"];
    }

    public function execute(array $parsed): void
    {
        $tableArg = $parsed['_positional'][0] ?? null;
        if (!$tableArg) { echo "✗ Se requiere '{db}.{table}'.\n"; return; }

        $conn = $this->resolveConnection($tableArg);
        if (!$conn) return;

        $structure = DB::select("DESCRIBE `{$conn['table']}`");
        if (empty($structure)) { StdOut::print("No structure found\r\n"); return; }

        StdOut::print("Structure of '{$conn['table']}' in '{$conn['db']}':\r\n\r\n");
        $this->displayAsTable($structure);
    }
}
