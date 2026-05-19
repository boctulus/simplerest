<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class ResetCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'reset';
        $this->description = 'Revierte TODAS las migraciones (equivale a rollback --all)';
        $this->aliases     = ['rollback-all'];
        $this->examples    = [
            'php com migrations reset',
            'php com migrations reset --to=db_main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['to', 'dir'],
            'flags'    => ['simulate'],
            'options'  => [
                'to'       => ['describe' => 'Conexión de base de datos'],
                'simulate' => ['describe' => 'Simular sin ejecutar cambios'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doReset(...$this->toOpt($parsed));
    }
}
