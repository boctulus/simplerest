<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeWidgetCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'widget';
        $this->description = 'Genera un widget';
        $this->aliases     = [];
        $this->examples    = [
            'php com make widget MyWidget',
            'php com make widget MyWidget --include-js',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['include-js', 'js', 'force'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del widget.\n"; return; }
        $this->widget($name, ...$this->toOpt($parsed));
    }
}
