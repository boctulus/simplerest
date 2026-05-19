<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeMiddlewareCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'middleware';
        $this->description = 'Genera un middleware';
        $this->aliases     = [];
        $this->examples    = [
            'php com make middleware MyMiddleware',
            'php com make middleware MyMiddleware --force',
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
        if (!$name) { echo "✗ Se requiere el nombre del middleware.\n"; return; }
        $this->delegate->middleware($name, ...$this->toOpt($parsed));
    }
}
