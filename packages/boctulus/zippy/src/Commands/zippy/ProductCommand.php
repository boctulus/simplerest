<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyProductCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'product';
        $this->description = 'Gestiona productos Zippy (subcomandos: process-one, process, batch, stats-categories, report-issues)';
        $this->aliases     = ['products'];
        $this->examples    = [
            'php com zippy product process-one',
            'php com zippy product process --limit=100',
            'php com zippy product batch',
            'php com zippy product stats-categories',
            'php com zippy product report-issues',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => [],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->product($this->subcommand($parsed), ...$this->subOpts($parsed));
    }
}
