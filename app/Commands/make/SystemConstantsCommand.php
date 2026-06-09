<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeSystemConstantsCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'system-constants';
        $this->description = 'Genera el archivo de constantes del sistema';
        $this->aliases     = ['sys-const'];
        $this->examples    = [
            'php com make system-constants',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => [],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->system_constants(...$this->toOpt($parsed));
    }
}
