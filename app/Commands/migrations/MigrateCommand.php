<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrateCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'migrate';
        $this->description = 'Ejecuta migraciones pendientes';
        $this->aliases     = ['up', 'run'];
        $this->examples    = [
            'php com migrations migrate',
            'php com migrations migrate --file=2021_09_13_users.php',
            'php com migrations migrate --dir=compania --to=db_153 --step=2',
            'php com migrations migrate --retry',
            'php com migrations migrate --simulate',
            'php com migrations migrate --make=schema,model',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['file', 'dir', 'folder', 'to', 'step', 'skip', 'make'],
            'flags'    => ['retry', 'force', 'ignore', 'fresh', 'debug', 'simulate', 'simulation'],
            'options'  => [
                'file'     => ['describe' => 'Archivo de migración específico'],
                'dir'      => ['describe' => 'Directorio de migraciones'],
                'to'       => ['describe' => 'Conexión de base de datos destino'],
                'step'     => ['describe' => 'Número de migraciones a ejecutar'],
                'skip'     => ['describe' => 'Número de migraciones a saltar'],
                'make'     => ['describe' => 'Generar después: schema, model, schema,model'],
                'retry'    => ['describe' => 'Re-ejecutar migraciones ya aplicadas'],
                'simulate' => ['describe' => 'Simular sin ejecutar cambios'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doMigrate(...$this->toOpt($parsed));
    }
}
