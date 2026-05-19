<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeControllerPackageCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'controller-package';
        $this->description = 'Genera un controlador dentro de un package';
        $this->aliases     = ['ctrl-package'];
        $this->examples    = [
            'php com make controller-package zippy MyController',
            'php com make controller-package zippy MyController --force',
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
        $this->delegate->controller_package(...$opt);
    }
}
