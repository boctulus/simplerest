<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MakeMigrationCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'make';
        $this->description = 'Crea un nuevo archivo de migración';
        $this->aliases     = ['create', 'new'];
        $this->examples    = [
            'php com migrations make my_table',
            'php com migrations make my_table --table=my_table --to=main',
            'php com migrations make brands --create',
            'php com migrations make brands --dir=giglio --to=giglio --create',
            'php com migrations make --class-name=Files --table=files',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dir', 'folder', 'table', 'class-name', 'to'],
            'flags'    => ['create', 'edit'],
            'options'  => [
                'dir'        => ['describe' => 'Directorio de salida'],
                'table'      => ['describe' => 'Nombre de la tabla'],
                'class-name' => ['describe' => 'Nombre de la clase'],
                'to'         => ['describe' => 'Conexión de base de datos'],
                'create'     => ['describe' => 'Incluir esquema de creación de tabla'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doMake(...$this->toOpt($parsed));
    }
}
