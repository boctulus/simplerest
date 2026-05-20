<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakePageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'page';
        $this->description = 'Genera una página';
        $this->aliases     = [];
        $this->examples    = [
            'php com make page admin/graficos',
            'php com make page admin/control_usuarios',
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
        if (!$name) { echo "✗ Se requiere el nombre de la página.\n"; return; }
        $this->page($name, ...$this->toOpt($parsed));
    }
}
