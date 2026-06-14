<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeMigrationFileCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'migration';
        $this->description = 'Crea un nuevo archivo de migración';
        $this->aliases     = ['migration-file'];
        $this->examples    = [
            'php com make migration my_table',
            'php com make migration brands --create',
            'php com make migration brands --dir=giglio --to=giglio --create',
            'php com make migration --class-name=Files --table=files --to=main',
            'php com make migration foo --drop-column=algun_campo',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dir', 'table', 'class-name', 'to', 'name',
                           'drop-column', 'rename-column', 'rename-table', 'nullable',
                           'drop-nullable', 'primary', 'add-unique', 'drop-unique',
                           'add-index', 'drop-index', 'drop-foreign', 'comment',
                           'from-field', 'to-field', 'to-table', 'constraint',
                           'on-delete', 'on-update'],
            'flags'    => ['create', 'edit', 'e', 'strict', 'remove', 'force'],
            'options'  => [
                'dir'        => ['describe' => 'Directorio de salida'],
                'table'      => ['describe' => 'Nombre de la tabla'],
                'class-name' => ['describe' => 'Nombre de la clase'],
                'to'         => ['describe' => 'Conexión de base de datos'],
                'create'     => ['describe' => 'Incluir esquema CREATE TABLE'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $opt = $this->toOpt($parsed);
        if ($name = $this->pos($parsed)) {
            array_unshift($opt, $name);
        }
        $this->migration(...$opt);
    }
}
