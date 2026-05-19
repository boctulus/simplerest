<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyCategoryCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'category';
        $this->description = 'Gestiona categorías Zippy (subcomandos: list-raw, test, create, set, merge, tree, resolve, find-dupes, report-issues, ...)';
        $this->aliases     = ['categories', 'cat'];
        $this->examples    = [
            'php com zippy category list-raw',
            'php com zippy category test --raw="Aceites Y Condimentos" --strategy=llm',
            'php com zippy category tree',
            'php com zippy category create --name=Electronics',
            'php com zippy category resolve',
            'php com zippy category find-dupes',
            'php com zippy category report-issues',
            'php com zippy category clear-cache',
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
