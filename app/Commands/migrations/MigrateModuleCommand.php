<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrateModuleCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'migrate-module';
        $this->description = 'Ejecuta migraciones de un módulo específico';
        $this->aliases     = ['module-migrate', 'migrate:module'];
        $this->examples    = [
            'php com migrations migrate-module FriendlyPOS',
            'php com migrations migrate-module FriendlyPOS --step=2',
            'php com migrations migrate-module FriendlyPOS --to=db_conn',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['step', 'to'],
            'flags'    => [],
            'options'  => [
                'step' => ['describe' => 'Número de migraciones a ejecutar'],
                'to'   => ['describe' => 'Conexión de base de datos destino'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $opt = $this->toOpt($parsed);
        $this->doMigrateModule(...$opt);
    }
}
