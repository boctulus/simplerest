<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeMigrationsPackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'migrations-package';
        $this->description = 'Crea un archivo de migración dentro de un package';
        $this->aliases     = ['migration-package'];
        $this->examples    = [
            'php com make migrations-package zippy categories --create',
            'php com make migrations-package zippy users --table=users --edit',
            'php com make migrations-package zippy categories --remove',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['table', 'class-name', 'to'],
            'flags'    => ['create', 'edit', 'e', 'remove'],
            'options'  => [
                'table'      => ['describe' => 'Nombre de la tabla'],
                'class-name' => ['describe' => 'Nombre de la clase'],
                'to'         => ['describe' => 'Conexión de base de datos'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->migrations_package(...$opt);
    }
}
