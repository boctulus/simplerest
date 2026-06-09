<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeSchemaCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'schema';
        $this->description = 'Genera archivo de Schema para una tabla o todas';
        $this->aliases     = ['make-schema'];
        $this->examples    = [
            'php com make schema my_table',
            'php com make schema all --from=main',
            'php com make schema all --from=mpo --except=migrations,users',
            'php com make schema gender --table=genders --from=mpo',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['from', 'table', 'except'],
            'flags'    => ['force', 'f', 'unignore', 'u', 'strict', 'remove'],
            'options'  => [
                'from'    => ['describe' => 'Conexión de base de datos origen'],
                'table'   => ['describe' => 'Nombre de tabla (si difiere del nombre)'],
                'except'  => ['describe' => 'Tablas a excluir (separadas por coma)'],
                'force'   => ['describe' => 'Sobreescribir si existe'],
                'strict'  => ['describe' => 'Modo estricto'],
                'remove'  => ['describe' => 'Eliminar el archivo'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed) ?? 'all';
        $this->schema($name, ...$this->toOpt($parsed));
    }
}
