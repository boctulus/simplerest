<?php

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\StdOut;

require_once __DIR__ . '/BaseSqlCommand.php';

class ListConnectionsCommand extends BaseSqlCommand
{
    public function __construct()
    {
        $this->command     = 'list-connections';
        $this->description = 'Lista todas las conexiones de base de datos configuradas';
        $this->aliases     = ['list-connexions', 'connections', 'connexions'];
        $this->examples    = [
            'php com sql list-connections',
            'php com sql list-connections --format=json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['format'],
            'flags'    => [],
            'options'  => ['format' => ['describe' => 'Formato: table, json', 'default' => 'table']],
        ];
    }

    public function execute(array $parsed): void
    {
        $format      = $this->opt($parsed, 'format', 'table');
        $config      = Config::get();
        $connections = $config['db_connections'] ?? [];

        $data = [];
        foreach ($connections as $connId => $connConfig) {
            $data[] = [
                'connection_id' => $connId,
                'db_name'       => $connConfig['db_name'] ?? 'N/A',
                'driver'        => $connConfig['driver']  ?? 'N/A',
            ];
        }

        if ($format === 'json') {
            StdOut::print(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\r\n");
            return;
        }

        if (empty($data)) { StdOut::print("No database connections found.\r\n"); return; }
        $this->displayAsTable($data);
    }
}
