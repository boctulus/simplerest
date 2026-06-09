<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeModelCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'model';
        $this->description = 'Genera archivo de Model para una tabla o todas';
        $this->aliases     = ['make-model'];
        $this->examples    = [
            'php com make model my_table',
            'php com make model my_table --force',
            'php com make model my_table --no-schema',
            'php com make model medios_transporte --no-schema --from=az',
            'php com make model all --from=main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['from'],
            'flags'    => ['force', 'f', 'unignore', 'u', 'no-check', 'no-verify', 'no-schema', 'x', 'strict', 'remove'],
            'options'  => [
                'from'      => ['describe' => 'Conexión de base de datos origen'],
                'force'     => ['describe' => 'Sobreescribir si existe'],
                'no-schema' => ['describe' => 'Generar sin schema'],
                'strict'    => ['describe' => 'Modo estricto'],
                'remove'    => ['describe' => 'Eliminar el archivo'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed) ?? 'all';
        $this->model($name, ...$this->toOpt($parsed));
    }
}
