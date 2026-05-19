<?php

use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class FindCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'find';
        $this->description = "Busca un registro por ID en '{db}.{table}'";
        $this->examples    = [
            "php com sql find 'main.users' --id=5",
            "php com sql find 'main.users' --id=5 --format=table",
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['id'],
            'optional' => ['format'],
            'flags'    => [],
            'options'  => [
                'id'     => ['describe' => 'Valor de la clave primaria'],
                'format' => ['describe' => 'Formato de salida: simple, table', 'default' => 'simple'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $tableArg = $parsed['_positional'][0] ?? null;
        $id       = $this->opt($parsed, 'id');
        $format   = $this->opt($parsed, 'format', 'simple');

        if (!$tableArg) {
            echo "✗ Se requiere '{db}.{table}' como primer argumento.\n";
            $this->showUsage();
            return;
        }

        $conn = $this->resolveConnection($tableArg);
        if (!$conn) return;

        $result = table($conn['table'])->find($id)->first();

        if ($result === null) {
            StdOut::print("No record found with ID '{$id}' in table '{$conn['table']}'\r\n");
            return;
        }

        $format === 'table'
            ? $this->displayAsTable([$result])
            : $this->displaySimple([$result]);
    }
}
