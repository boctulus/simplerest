<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyOllamaCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'ollama';
        $this->description = 'Comandos Zippy integrados con Ollama/LLM';
        $this->aliases     = [];
        $this->examples    = [
            'php com zippy ollama test-strategy',
            'php com zippy ollama hard-tests',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => [], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->ollama($this->subcommand($parsed), ...$this->subOpts($parsed));
    }
}
