<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeModelModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'model-module';
        $this->description = 'Genera un Model dentro de un módulo';
        $this->aliases     = [];
        $this->examples    = ['php com make model-module FriendlyPOS products --force'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f', 'strict', 'remove'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->model_module(...$opt);
    }
}
