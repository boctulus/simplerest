<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyMapStatsCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'map-stats';
        $this->description = 'Muestra estadísticas del mapeo de categorías';
        $this->aliases     = ['stats'];
        $this->examples    = [
            'php com zippy map-stats',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => [], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->map_stats();
    }
}
