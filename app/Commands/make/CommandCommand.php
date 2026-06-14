<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeCommandCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'command';
        $this->description = 'Genera un comando CLI';
        $this->aliases     = ['cmd'];
        $this->examples    = [
            'php com make command myCommand',
            'php com make command myCommand --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del comando.\n"; return; }
        $this->command($name, ...$this->toOpt($parsed));
    }
}
