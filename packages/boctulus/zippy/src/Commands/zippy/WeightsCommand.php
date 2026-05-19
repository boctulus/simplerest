<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyWeightsCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'weights';
        $this->description = 'Gestiona pesos/ponderaciones en Zippy';
        $this->aliases     = [];
        $this->examples    = [
            'php com zippy weights list',
            'php com zippy weights seed',
            'php com zippy weights train',
            'php com zippy weights clear',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => [], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->weights($this->subcommand($parsed), ...$this->subOpts($parsed));
    }
}
