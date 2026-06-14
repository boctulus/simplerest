<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeConsoleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'console';
        $this->description = 'Genera un controlador de consola';
        $this->aliases     = [];
        $this->examples    = [
            'php com make console my_console_ctrl',
            'php com make console folder/my_console_ctrl --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f', 'unignore', 'u', 'strict', 'remove'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del controlador de consola.\n"; return; }
        $this->console($name, ...$this->toOpt($parsed));
    }
}
