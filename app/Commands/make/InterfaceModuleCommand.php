<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeInterfaceModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'interface-module';
        $this->description = 'Genera una interfaz dentro de un módulo';
        $this->aliases     = [];
        $this->examples    = ['php com make interface-module FriendlyPOS IMyInterface'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->interface_module(...$opt);
    }
}
