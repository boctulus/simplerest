<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

/**
 * Stress-test: string option, numeric option, boolean flag, quoted values,
 *              both --key=value and --key:value separators.
 *
 * php com test echo --msg="Hello World" --count=3 --upper
 * php com test echo --msg:Hola          --count:2
 * php com test echo --msg="Test"        --count=1
 */
class EchoCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'echo';
        $this->description = 'Repite un mensaje N veces (prueba opciones, flags y separadores)';
        $this->aliases     = ['repeat', 'say'];
        $this->examples    = [
            'php com test echo --msg="Hello World" --count=3 --upper',
            'php com test echo --msg:Hola          --count:2',
            'php com test echo --msg="Test spaces" --count=1 --upper',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['msg'],
            'optional' => ['count'],
            'flags'    => ['upper', 'verbose'],
            'options'  => [
                'msg'     => ['describe' => 'Mensaje a repetir'],
                'count'   => ['describe' => 'Número de repeticiones', 'default' => 1],
                'upper'   => ['describe' => 'Convertir a mayúsculas'],
                'verbose' => ['describe' => 'Mostrar información adicional'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $msg     = $this->opt($parsed, 'msg');
        $count   = (int) $this->opt($parsed, 'count', 1);
        $upper   = $this->opt($parsed, 'upper', false);
        $verbose = $this->opt($parsed, 'verbose', false);

        if ($upper) {
            $msg = strtoupper($msg);
        }

        if ($verbose) {
            echo "Mensaje: '{$msg}'\n";
            echo "Repeticiones: {$count}\n";
            echo "Mayúsculas: " . ($upper ? 'sí' : 'no') . "\n";
            echo str_repeat('-', 30) . "\n";
        }

        for ($i = 1; $i <= $count; $i++) {
            echo "[{$i}] {$msg}\n";
        }
    }
}
