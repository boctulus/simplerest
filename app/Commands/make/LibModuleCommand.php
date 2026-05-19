<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeLibModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'lib-module';
        $this->description = 'Genera una librería dentro de un módulo';
        $this->aliases     = [];
        $this->examples    = ['php com make lib-module FriendlyPOS MyLib'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->delegate->lib_module(...$opt);
    }
}
