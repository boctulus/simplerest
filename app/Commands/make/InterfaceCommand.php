<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeInterfaceCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'interface';
        $this->description = 'Genera una interfaz';
        $this->aliases     = [];
        $this->examples    = [
            'php com make interface SomeClass',
            'php com make interface OpenFactura --from=D:/path/to/OpenFacturaSDK.php',
            'php com make interface pluggable --remove',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['from'],
            'flags'    => ['force', 'f', 'unignore', 'u', 'strict', 'remove'],
            'options'  => [
                'from' => ['describe' => 'Archivo fuente para generar la interfaz'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre de la interfaz.\n"; return; }
        $this->interface($name, ...$this->toOpt($parsed));
    }
}
