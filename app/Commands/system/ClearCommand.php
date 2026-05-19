<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class ClearCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'clear';
        $this->description = 'Dump autoload y limpia cache de Composer';
        $this->aliases     = ['cache-clear'];
        $this->examples    = ['php com system clear'];
    }

    public function execute(array $parsed): void
    {
        echo "Ejecutando composer dump-autoload -o ...\n";
        exec('composer dump-autoload -o', $out1, $ret1);

        echo "Ejecutando composer clear-cache ...\n";
        exec('composer clear-cache', $out2, $ret2);

        if ($ret1 === 0 && $ret2 === 0) {
            echo "✓ Cache limpiado y autoload regenerado.\n";
        } else {
            echo "✗ Error al ejecutar composer. Código: dump={$ret1}, clear={$ret2}\n";
        }
    }
}
