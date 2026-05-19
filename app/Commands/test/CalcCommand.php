<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

/**
 * Stress-test: positional args as sub-operation, numeric args, mixed styles.
 *
 * php com test calc add 5 10
 * php com test calc sub 20 7
 * php com test calc --op=multiply --a=3 --b=4
 * php com test calc div --a=100 --b=4
 */
class CalcCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'calc';
        $this->description = 'Calculadora simple (prueba args posicionales numéricos y mixtos)';
        $this->aliases     = ['math', 'calculate'];
        $this->examples    = [
            'php com test calc add 5 10',
            'php com test calc sub 20 7',
            'php com test calc --op=multiply --a=3 --b=4',
            'php com test calc div --a=100 --b=4',
            'php com test calc mod 17 5',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['op', 'a', 'b'],
            'flags'    => [],
            'options'  => [
                'op' => ['describe' => 'Operación: add, sub, multiply, div, mod'],
                'a'  => ['describe' => 'Primer operando'],
                'b'  => ['describe' => 'Segundo operando'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $positional = $parsed['_positional'] ?? [];

        // Support positional: calc add 5 10  OR  calc --op=add --a=5 --b=10
        $op = $this->opt($parsed, 'op') ?? ($positional[0] ?? null);
        $a  = $this->opt($parsed, 'a')  ?? ($positional[1] ?? null);
        $b  = $this->opt($parsed, 'b')  ?? ($positional[2] ?? null);

        if (!$op) {
            echo "✗ Falta la operación. Uso: php com test calc <op> <a> <b>\n";
            $this->showUsage();
            return;
        }

        if ($a === null || $b === null) {
            echo "✗ Se requieren dos operandos (--a y --b, o posicionales).\n";
            $this->showUsage();
            return;
        }

        $a = (float) $a;
        $b = (float) $b;

        switch ($op) {
            case 'add':
            case '+':
                $result = $a + $b;
                $symbol = '+';
                break;
            case 'sub':
            case '-':
                $result = $a - $b;
                $symbol = '-';
                break;
            case 'multiply':
            case 'mul':
            case '*':
                $result = $a * $b;
                $symbol = '×';
                break;
            case 'div':
            case '/':
                if ($b == 0) {
                    echo "✗ División por cero.\n";
                    return;
                }
                $result = $a / $b;
                $symbol = '÷';
                break;
            case 'mod':
            case '%':
                if ($b == 0) {
                    echo "✗ Módulo por cero.\n";
                    return;
                }
                $result = fmod($a, $b);
                $symbol = '%';
                break;
            default:
                echo "✗ Operación '{$op}' no soportada. Usa: add, sub, multiply, div, mod\n";
                return;
        }

        $aFmt = rtrim(rtrim(number_format($a, 10, '.', ''), '0'), '.');
        $bFmt = rtrim(rtrim(number_format($b, 10, '.', ''), '0'), '.');
        $rFmt = rtrim(rtrim(number_format($result, 10, '.', ''), '0'), '.');

        echo "{$aFmt} {$symbol} {$bFmt} = {$rFmt}\n";
    }
}
