<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrationsStatusCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command = 'status';
        $this->description = 'Compara archivos de migración con sus registros en la base de datos';
        $this->aliases = ['st'];
        $this->examples = [
            'php com migrations status',
            'php com migrations status --to=db_195',
            'php com migrations status --dir=edu',
            'php com migrations status --full-discovery',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['to', 'dir'],
            'flags' => ['full-discovery'],
            'options' => [
                'to' => ['describe' => 'Conexión de base de datos'],
                'dir' => ['describe' => 'Subdirectorio dentro de database/migrations/'],
                'full-discovery' => ['describe' => 'Compara migraciones de la aplicación, módulos y paquetes'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doStatus(...$this->toOpt($parsed));
    }
}
