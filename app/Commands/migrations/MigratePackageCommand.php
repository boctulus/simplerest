<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigratePackageCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'migrate-package';
        $this->description = 'Ejecuta migraciones de un package específico';
        $this->aliases     = ['package-migrate', 'migrate:package'];
        $this->examples    = [
            'php com migrations migrate-package zippy',
            'php com migrations migrate-package zippy --step=2',
            'php com migrations migrate-package zippy --to=db_conn',
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
        $this->doMigratePackage(...$opt);
    }
}
