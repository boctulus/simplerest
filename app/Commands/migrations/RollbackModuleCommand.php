<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RollbackModuleCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'rollback-module';
        $this->description = 'Revierte migraciones de un módulo específico';
        $this->aliases     = ['module-rollback', 'rollback:module'];
        $this->examples    = [
            'php com migrations rollback-module FriendlyPOS',
            'php com migrations rollback-module FriendlyPOS --step=2',
            'php com migrations rollback-module FriendlyPOS --all',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['step'],
            'flags'    => ['all'],
            'options'  => [
                'step' => ['describe' => 'Número de migraciones a revertir'],
                'all'  => ['describe' => 'Revertir todas las migraciones del módulo'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $opt = $this->toOpt($parsed);
        $this->doRollbackModule(...$opt);
    }
}
