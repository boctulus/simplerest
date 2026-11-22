<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrationsListOnDbCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command = 'list-on-db';
        $this->description = 'Lista las migraciones registradas en la tabla migrations';
        $this->aliases = ['db-list'];
        $this->examples = [
            'php com migrations list-on-db',
            'php com migrations list-on-db --to=db_195',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['to'],
            'flags' => [],
            'options' => [
                'to' => ['describe' => 'Conexión de base de datos registrada'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doListOnDb(...$this->toOpt($parsed));
    }
}
