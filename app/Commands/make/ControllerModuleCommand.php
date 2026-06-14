<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeControllerModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'controller-module';
        $this->description = 'Genera un controlador dentro de un módulo';
        $this->aliases     = ['ctrl-module'];
        $this->examples    = [
            'php com make controller-module FriendlyPOS MyController',
            'php com make controller-module FriendlyPOS MyController --force',
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
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->controller_module(...$opt);
    }
}
