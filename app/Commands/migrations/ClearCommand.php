<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrationsClearCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'clear';
        $this->description = 'Limpia los registros de la tabla migrations (sin revertir DDL)';
        $this->aliases     = ['clear-log'];
        $this->examples    = [
            'php com migrations clear',
            'php com migrations clear --to=db_195',
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
        $this->doClear(...$this->toOpt($parsed));
    }
}
