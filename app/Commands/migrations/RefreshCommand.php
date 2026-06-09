<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RefreshCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'refresh';
        $this->description = 'Revierte todas las migraciones y las vuelve a ejecutar';
        $this->aliases     = [];
        $this->examples    = [
            'php com migrations refresh',
            'php com migrations refresh --to=db_main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['to'],
            'flags'    => [],
            'options'  => [
                'to' => ['describe' => 'Conexión de base de datos'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doRefresh(...$this->toOpt($parsed));
    }
}
