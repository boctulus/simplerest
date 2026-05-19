<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RollbackPackageCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'rollback-package';
        $this->description = 'Revierte migraciones de un package específico';
        $this->aliases     = ['package-rollback', 'rollback:package'];
        $this->examples    = [
            'php com migrations rollback-package zippy',
            'php com migrations rollback-package zippy --step=1',
            'php com migrations rollback-package zippy --all',
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
                'all'  => ['describe' => 'Revertir todas las migraciones del package'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $opt = $this->toOpt($parsed);
        $this->doRollbackPackage(...$opt);
    }
}
