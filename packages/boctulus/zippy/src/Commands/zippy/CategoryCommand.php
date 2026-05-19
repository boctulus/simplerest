<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyCategoryCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'category';
        $this->description = 'Gestiona categorías Zippy (subcomandos: list, sync, map, ...)';
        $this->aliases     = ['categories', 'cat'];
        $this->examples    = [
            'php com zippy category list',
            'php com zippy category sync',
            'php com zippy category map --id=5',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => [], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->category($this->subcommand($parsed), ...$this->subOpts($parsed));
    }
}
