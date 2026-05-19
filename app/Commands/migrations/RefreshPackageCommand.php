<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class RefreshPackageCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'refresh-package';
        $this->description = 'Revierte y re-aplica migraciones de un package (rollback-all + migrate)';
        $this->aliases     = ['package-refresh', 'refresh:package'];
        $this->examples    = [
            'php com migrations refresh-package zippy',
            'php com migrations refresh-package zippy --to=db_conn',
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
        $this->doRefreshPackage(...$opt);
    }
}
