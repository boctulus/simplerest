<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class OpcacheClearCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'opcache-clear';
        $this->description = 'Limpia el OPcache de PHP';
        $this->aliases     = ['opcache'];
        $this->examples    = ['php com system opcache-clear'];
    }

    public function execute(array $parsed): void
    {
        if (!function_exists('opcache_reset')) {
            echo "✗ opcache_reset() no disponible en este entorno.\n";
            return;
        }

        $ok = opcache_reset();
        echo $ok ? "✓ OPCache limpiado.\n" : "✗ No se pudo limpiar el OPCache.\n";
    }
}
