<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeMigrationsModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'migrations-module';
        $this->description = 'Crea un archivo de migración dentro de un módulo';
        $this->aliases     = ['migration-module'];
        $this->examples    = [
            'php com make migrations-module FriendlyPOS products --create',
            'php com make migrations-module FriendlyPOS users --table=users --edit',
            'php com make migrations-module FriendlyPOS products --remove',
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
        $this->migrations_module(...$opt);
    }
}
