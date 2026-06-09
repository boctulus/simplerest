<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeViewCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'view';
        $this->description = 'Genera una vista';
        $this->aliases     = [];
        $this->examples    = [
            'php com make view my_view',
            'php com make view my_view --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f', 'unignore', 'u', 'remove'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre de la vista.\n"; return; }
        $this->view($name, ...$this->toOpt($parsed));
    }
}
