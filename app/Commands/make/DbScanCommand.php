<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeDbScanCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'db-scan';
        $this->description = 'Escanea la base de datos y genera schemas/models';
        $this->aliases     = ['scan'];
        $this->examples    = [
            'php com make db-scan',
            'php com make db-scan --from=main',
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
        $this->db_scan(...$this->toOpt($parsed));
    }
}
