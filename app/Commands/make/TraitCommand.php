<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeTraitCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'trait';
        $this->description = 'Genera un trait';
        $this->aliases     = [];
        $this->examples    = [
            'php com make trait MyTrait',
            'php com make trait MyTrait --force',
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
        if (!$name) { echo "✗ Se requiere el nombre del trait.\n"; return; }
        $this->trait($name, ...$this->toOpt($parsed));
    }
}
