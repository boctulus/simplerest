<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeHelperCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'helper';
        $this->description = 'Genera un helper';
        $this->aliases     = [];
        $this->examples    = [
            'php com make helper my_helper',
            'php com make helper my_helper --force',
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
        if (!$name) { echo "✗ Se requiere el nombre del helper.\n"; return; }
        $this->helper($name, ...$this->toOpt($parsed));
    }
}
