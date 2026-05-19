<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\StdOut;

abstract class BaseSqlCommand extends BaseCommand
{
    protected function resolveConnection(string $tableArg): ?array
    {
        if (!Strings::contains('.', $tableArg)) {
            return null;
        }
        [$db, $table] = explode('.', $tableArg, 2);

        if (!DB::connectionExists($db)) {
            echo "✗ Conexión '{$db}' no registrada en db_connections.\n";
            return null;
        }

        DB::setConnection($db);
        return ['db' => $db, 'table' => $table];
    }

    protected function displaySimple(array $results): void
    {
        $count = count($results);
        StdOut::print("Found {$count} record(s):\r\n\r\n");
        foreach ($results as $i => $row) {
            StdOut::print("Record #" . ($i + 1) . ":\r\n");
            foreach ($row as $key => $value) {
                StdOut::print("  {$key}: " . ($value === null ? 'NULL' : $value) . "\r\n");
            }
            StdOut::print("\r\n");
        }
    }

    protected function displayAsTable(array $results): void
    {
        if (empty($results)) return;

        $columns = array_keys($results[0]);
        $widths  = array_fill_keys($columns, 0);
        foreach ($columns as $col) $widths[$col] = strlen($col);
        foreach ($results as $row) {
            foreach ($row as $col => $value) {
                $widths[$col] = max($widths[$col], strlen((string)($value ?? 'NULL')));
            }
        }

        $sep = '+' . implode('+', array_map(fn($w) => str_repeat('-', $w + 2), $widths)) . '+';
        StdOut::print("{$sep}\r\n");
        $header = '|' . implode('|', array_map(fn($col, $w) => ' ' . str_pad($col, $w) . ' ', $columns, $widths)) . '|';
        StdOut::print("{$header}\r\n");
        StdOut::print("{$sep}\r\n");
        foreach ($results as $row) {
            $line = '|' . implode('|', array_map(fn($col, $w) => ' ' . str_pad((string)($row[$col] ?? 'NULL'), $w) . ' ', $columns, $widths)) . '|';
            StdOut::print("{$line}\r\n");
        }
        StdOut::print("{$sep}\r\n");
        StdOut::print("\r\n" . count($results) . " record(s) found\r\n");
    }
}
