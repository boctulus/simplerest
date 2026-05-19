<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrationsListCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'list';
        $this->description = 'Lista archivos de migración disponibles';
        $this->aliases     = ['ls'];
        $this->examples    = [
            'php com migrations list',
            'php com migrations list --dir=edu',
            'php com migrations list --dir=edu --table=tags',
            'php com migrations list --contains=user',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dir', 'table', 'contains'],
            'flags'    => [],
            'options'  => [
                'dir'      => ['describe' => 'Subdirectorio dentro de migrations/'],
                'table'    => ['describe' => 'Filtrar por nombre de tabla'],
                'contains' => ['describe' => 'Filtrar por substring en nombre de archivo o clase'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doList(...$this->toOpt($parsed));
    }
}
