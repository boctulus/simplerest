<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyProductCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'product';
        $this->description = 'Gestiona productos Zippy (subcomandos: process-one, sync, list, ...)';
        $this->aliases     = ['products'];
        $this->examples    = [
            'php com zippy product process-one',
            'php com zippy product sync --limit=100',
            'php com zippy product list',
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
