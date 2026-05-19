<?php

use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class ExportCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'export';
        $this->description = "Exporta datos de '{db}.{table}' a CSV o JSON";
        $this->examples    = [
            "php com sql export 'main.users' --format=csv",
            "php com sql export 'main.users' --format=json --path=/tmp/users.json",
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['format', 'path', 'file'],
            'flags'    => [],
            'options'  => [
                'format' => ['describe' => 'Formato: csv, json', 'default' => 'csv'],
                'path'   => ['describe' => 'Ruta del archivo de salida'],
                'file'   => ['describe' => 'Alias de --path'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $tableArg = $parsed['_positional'][0] ?? null;
        $format   = $this->opt($parsed, 'format', 'csv');
        $path     = $this->opt($parsed, 'path') ?? $this->opt($parsed, 'file');

        if (!$tableArg) { echo "✗ Se requiere '{db}.{table}'.\n"; return; }
        if (!in_array($format, ['csv', 'json'])) { echo "✗ Formato debe ser 'csv' o 'json'.\n"; return; }

        $conn = $this->resolveConnection($tableArg);
        if (!$conn) return;

        $results = table($conn['table'])->get();
        if (empty($results)) { StdOut::print("No records found\r\n"); return; }

        if (!$path) {
            $path = "exports/{$conn['table']}_" . date('Y-m-d_H-i-s') . ".{$format}";
        }

        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            echo "✗ No se pudo crear el directorio '{$dir}'.\n";
            return;
        }

        if ($format === 'csv') {
            $file = fopen($path, 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, array_keys($results[0]));
            foreach ($results as $row) {
                fputcsv($file, array_map(fn($v) => $v ?? '', $row));
            }
            fclose($file);
        } else {
            file_put_contents($path, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        StdOut::print("✓ Exportado {$conn['table']} ({$conn['db']}) → {$path} (" . count($results) . " registros)\r\n");
    }
}
