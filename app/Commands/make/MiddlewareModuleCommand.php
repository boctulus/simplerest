<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeMiddlewareModuleCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'middleware-module';
        $this->description = 'Genera un middleware dentro de un módulo';
        $this->aliases     = [];
        $this->examples    = [
            'php com make middleware-module FriendlyPOS MyMiddleware',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->delegate->middleware_module(...$opt);
    }
}
