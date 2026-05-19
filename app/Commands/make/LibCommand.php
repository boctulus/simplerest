<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeLibCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'lib';
        $this->description = 'Genera una librería';
        $this->aliases     = [];
        $this->examples    = [
            'php com make lib my_lib',
            'php com make lib my_folder/my_lib --force',
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
        if (!$name) { echo "✗ Se requiere el nombre de la librería.\n"; return; }
        $this->delegate->lib($name, ...$this->toOpt($parsed));
    }
}
