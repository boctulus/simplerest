<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class FreshCommand extends BaseMigrationsCommand
{
    public function __construct()
    {
        $this->command     = 'fresh';
        $this->description = 'Elimina todas las tablas y re-migra (DESTRUCTIVO, requiere --force y --to=)';
        $this->aliases     = [];
        $this->examples    = [
            'php com migrations fresh --to=db_195 --force',
            'php com migrations fresh --to=the_tenant --force --migrate',
            'php com migrations fresh --dir=compania --to=db_149 --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['to'],
            'optional' => ['file', 'dir', 'folder'],
            'flags'    => ['force', 'migrate'],
            'options'  => [
                'to'      => ['describe' => 'Conexión de base de datos destino (OBLIGATORIO)'],
                'file'    => ['describe' => 'Archivo de migración específico'],
                'dir'     => ['describe' => 'Directorio de migraciones'],
                'force'   => ['describe' => 'Confirmar operación destructiva (OBLIGATORIO)'],
                'migrate' => ['describe' => 'Ejecutar migraciones después de limpiar'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->doFresh(...$this->toOpt($parsed));
    }
}
