<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyBrandCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'brand';
        $this->description = 'Gestiona marcas Zippy (subcomandos: list, sync, map, ...)';
        $this->aliases     = ['brands'];
        $this->examples    = [
            'php com zippy brand list',
            'php com zippy brand sync',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => [], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->brand($this->subcommand($parsed), ...$this->subOpts($parsed));
    }
}
