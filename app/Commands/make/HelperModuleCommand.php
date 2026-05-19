<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeHelperModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'helper-module';
        $this->description = 'Genera un helper dentro de un módulo';
        $this->aliases     = [];
        $this->examples    = ['php com make helper-module FriendlyPOS my_helper'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->delegate->helper_module(...$opt);
    }
}
