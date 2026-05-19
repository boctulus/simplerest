<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RollbackCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'rollback';
        $this->description = 'Revierte la(s) última(s) migración(es)';
        $this->aliases     = ['down'];
        $this->examples    = [
            'php com migrations rollback',
            'php com migrations rollback --step=3',
            'php com migrations rollback --all --to=db_195',
            'php com migrations rollback --file=2021_09_14_files.php --to=main',
            'php com migrations rollback --dir=compania --to=db_195 --simulate',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['file', 'dir', 'folder', 'to', 'step', 'steps', 'n'],
            'flags'    => ['all', 'simulate', 'sim', 'simulation'],
            'options'  => [
                'file'     => ['describe' => 'Archivo de migración específico'],
                'dir'      => ['describe' => 'Directorio de migraciones'],
                'to'       => ['describe' => 'Conexión de base de datos'],
                'step'     => ['describe' => 'Número de migraciones a revertir (default: 1)'],
                'all'      => ['describe' => 'Revertir todas las migraciones'],
                'simulate' => ['describe' => 'Simular sin ejecutar cambios'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doRollback(...$this->toOpt($parsed));
    }
}
