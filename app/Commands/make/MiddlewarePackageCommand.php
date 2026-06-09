<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeMiddlewarePackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'middleware-package';
        $this->description = 'Genera un middleware dentro de un package';
        $this->aliases     = [];
        $this->examples    = [
            'php com make middleware-package zippy MyMiddleware',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->middleware_package(...$opt);
    }
}
