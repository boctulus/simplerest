<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeRelationScanCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'relation-scan';
        $this->description = 'Escanea y genera relaciones entre modelos';
        $this->aliases     = ['rel-scan', 'relation_scan'];
        $this->examples    = [
            'php com make relation-scan',
            'php com make relation-scan --from=main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['from'],
            'flags'    => [],
            'options'  => [
                'from' => ['describe' => 'Conexión de base de datos'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->relation_scan(...$this->toOpt($parsed));
    }
}
