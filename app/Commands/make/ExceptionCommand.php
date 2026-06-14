<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeExceptionCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'exception';
        $this->description = 'Genera una clase de excepción';
        $this->aliases     = [];
        $this->examples    = [
            'php com make exception MyException',
            'php com make exception MyException --force',
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
        if (!$name) { echo "✗ Se requiere el nombre de la excepción.\n"; return; }
        $this->exception($name, ...$this->toOpt($parsed));
    }
}
