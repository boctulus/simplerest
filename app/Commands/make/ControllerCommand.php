<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeControllerCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'controller';
        $this->description = 'Genera un controlador';
        $this->aliases     = ['ctrl'];
        $this->examples    = [
            'php com make controller my_controller',
            'php com make controller folder/my_controller --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f', 'unignore', 'u', 'strict', 'remove'],
            'options'  => [
                'force'  => ['describe' => 'Sobreescribir si existe'],
                'strict' => ['describe' => 'Modo estricto'],
                'remove' => ['describe' => 'Eliminar el archivo'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del controlador.\n"; return; }
        $this->delegate->controller($name, ...$this->toOpt($parsed));
    }
}
