<?php

use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class FindByCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'find-by';
        $this->description = "Busca registros por campo=valor en '{db}.{table}'";
        $this->aliases     = ['find_by'];
        $this->examples    = [
            "php com sql find-by 'main.users' --field=email --value=john@example.com",
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['field', 'value'],
            'optional' => ['format'],
            'flags'    => [],
            'options'  => [
                'field'  => ['describe' => 'Nombre del campo'],
                'value'  => ['describe' => 'Valor a buscar'],
                'format' => ['describe' => 'Formato: simple, table', 'default' => 'simple'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $tableArg = $parsed['_positional'][0] ?? null;
        $field    = $this->opt($parsed, 'field');
        $value    = $this->opt($parsed, 'value');
        $format   = $this->opt($parsed, 'format', 'simple');

        if (!$tableArg) {
            echo "✗ Se requiere '{db}.{table}' como primer argumento.\n";
            $this->showUsage();
            return;
        }

        $conn = $this->resolveConnection($tableArg);
        if (!$conn) return;

        $results = table($conn['table'])->where([$field => $value])->get();

        if (empty($results)) {
            StdOut::print("No records found with {$field} = '{$value}'\r\n");
            return;
        }

        $format === 'table'
            ? $this->displayAsTable($results)
            : $this->displaySimple($results);
    }
}
