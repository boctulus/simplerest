<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeHelperPackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'helper-package';
        $this->description = 'Genera un helper dentro de un package';
        $this->aliases     = [];
        $this->examples    = ['php com make helper-package zippy my_helper'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->helper_package(...$opt);
    }
}
