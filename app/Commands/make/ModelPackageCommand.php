<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeModelPackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'model-package';
        $this->description = 'Genera un Model dentro de un package';
        $this->aliases     = [];
        $this->examples    = ['php com make model-package zippy products --force'];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => ['force', 'f', 'strict', 'remove'], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $opt = array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
        $this->delegate->model_package(...$opt);
    }
}
