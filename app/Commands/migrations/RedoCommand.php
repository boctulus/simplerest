<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RedoCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'redo';
        $this->description = 'Revierte y re-aplica una migración específica';
        $this->aliases     = [];
        $this->examples    = [
            'php com migrations redo --file=2021_09_14_files.php --to=main',
            'php com migrations redo --to=db_195 --simulate',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['file', 'to'],
            'flags'    => ['simulate', 'simulation'],
            'options'  => [
                'file'     => ['describe' => 'Archivo de migración específico'],
                'to'       => ['describe' => 'Conexión de base de datos'],
                'simulate' => ['describe' => 'Simular sin ejecutar cambios'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doRedo(...$this->toOpt($parsed));
    }
}
