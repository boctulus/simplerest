<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeLibPackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'lib-package';
        $this->description = 'Genera una librería dentro de un package';
        $this->aliases     = [];
        $this->examples    = ['php com make lib-package zippy MyLib'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->lib_package(...$opt);
    }
}
