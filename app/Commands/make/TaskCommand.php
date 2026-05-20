<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeTaskCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'task';
        $this->description = 'Genera una Task';
        $this->aliases     = [];
        $this->examples    = [
            'php com make task MyTask',
            'php com make task MyTask --force',
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
        if (!$name) { echo "✗ Se requiere el nombre de la Task.\n"; return; }
        $this->task($name, ...$this->toOpt($parsed));
    }
}
