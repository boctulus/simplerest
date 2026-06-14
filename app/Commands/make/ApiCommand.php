<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeApiCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'api';
        $this->description = 'Genera controlador API RESTful para una tabla o todas';
        $this->aliases     = ['make-api'];
        $this->examples    = [
            'php com make api my_table',
            'php com make api my_table --from=main --force',
            'php com make api all --from=some_conn_id --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['from'],
            'flags'    => ['force', 'f', 'unignore', 'u', 'strict', 'remove'],
            'options'  => [
                'from'   => ['describe' => 'Conexión de base de datos origen'],
                'force'  => ['describe' => 'Sobreescribir si existe'],
                'strict' => ['describe' => 'Modo estricto'],
                'remove' => ['describe' => 'Eliminar el archivo'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed) ?? 'all';
        $this->api($name, ...$this->toOpt($parsed));
    }
}
