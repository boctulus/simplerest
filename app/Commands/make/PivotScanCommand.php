<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakePivotScanCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'pivot-scan';
        $this->description = 'Escanea y genera archivo de pivots entre tablas';
        $this->aliases     = ['pivot_scan'];
        $this->examples    = [
            'php com make pivot-scan',
            'php com make pivot-scan --from=main',
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
        $this->pivot_scan(...$this->toOpt($parsed));
    }
}
