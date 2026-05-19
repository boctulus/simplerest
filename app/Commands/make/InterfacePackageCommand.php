<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeInterfacePackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'interface-package';
        $this->description = 'Genera una interfaz dentro de un package';
        $this->aliases     = [];
        $this->examples    = ['php com make interface-package zippy IMyInterface'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->delegate->interface_package(...$opt);
    }
}
