<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RefreshModuleCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'refresh-module';
        $this->description = 'Revierte y re-aplica migraciones de un módulo (rollback-all + migrate)';
        $this->aliases     = ['module-refresh', 'refresh:module'];
        $this->examples    = [
            'php com migrations refresh-module FriendlyPOS',
            'php com migrations refresh-module FriendlyPOS --to=db_conn',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['to'],
            'flags'    => [],
            'options'  => [
                'to' => ['describe' => 'Conexión de base de datos destino'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $opt = $this->toOpt($parsed);
        $this->doRefreshModule(...$opt);
    }
}
