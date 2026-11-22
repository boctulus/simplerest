<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrationsListCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'list-files';
        $this->description = 'Lista archivos de migración disponibles';
        $this->aliases     = ['list', 'ls'];
        $this->examples    = [
            'php com migrations list-files',
            'php com migrations list-files --dir=edu',
            'php com migrations list-files --full-discovery',
            'php com migrations list-files --dir=edu --table=tags',
            'php com migrations list-files --contains=user',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dir', 'table', 'contains'],
            'flags'    => ['full-discovery'],
            'options'  => [
                'dir'      => ['describe' => 'Subdirectorio dentro de migrations/'],
                'table'    => ['describe' => 'Filtrar por nombre de tabla'],
                'contains' => ['describe' => 'Filtrar por substring en nombre de archivo o clase'],
                'full-discovery' => ['describe' => 'Incluye migraciones de módulos y paquetes'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doList(...$this->toOpt($parsed));
    }
}
