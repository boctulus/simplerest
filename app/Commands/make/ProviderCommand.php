<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeProviderCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'provider';
        $this->description = 'Genera un ServiceProvider';
        $this->aliases     = ['service-provider', 'service'];
        $this->examples    = [
            'php com make provider MyServiceProvider',
            'php com make provider MyServiceProvider --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f', 'unignore', 'u'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del ServiceProvider.\n"; return; }
        $this->provider($name, ...$this->toOpt($parsed));
    }
}
