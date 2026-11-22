<?php

use Boctulus\Simplerest\Core\Libs\Files;

require_once __DIR__ . '/BaseFileCommand.php';

class FileDeleteCommand extends BaseFileCommand
{
    public function __construct()
    {
        $this->command     = 'delete';
        $this->description = 'Elimina archivos de un directorio';
        $this->aliases     = ['rm', 'remove'];
        $this->examples    = [
            'php com file delete app/Locale/ --pattern=validator.* --recursive --dry-run',
            'php com file delete app/Locale/ --pattern=mutawp.* --recursive --force',
            'php com file delete . --pattern=*.tmp',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['pattern', 'exclude'],
            'flags'    => ['recursive', 'dry-run', 'force'],
            'options'  => [
                'pattern'   => ['describe' => 'Filtro de patrón (ej: *.php, *.php|*.js)', 'default' => '*.*'],
                'exclude'   => ['describe' => 'Patrón a excluir'],
                'recursive' => ['describe' => 'Buscar recursivamente'],
                'dry-run'   => ['describe' => 'Mostrar qué se eliminaría sin borrar'],
                'force'     => ['describe' => 'Eliminar sin confirmación'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $dryRun  = $this->opt($parsed, 'dry_run', false);
        $force   = $this->opt($parsed, 'force', false);
        $dir     = $parsed['_positional'][0] ?? $this->opt($parsed, 'dir', '.');

        $files = $this->getFileEntries($parsed);

        if (empty($files)) {
            if (!is_dir(Files::addTrailingSlash($dir))) {
                $this->log("El directorio '{$dir}' no existe.", 'error');
            } else {
                $this->log(" No se encontraron archivos para eliminar.", 'warning');
            }
            return;
        }

        $count = count($files);

        if ($dryRun) {
            $this->log("Se eliminarían {$count} archivo(s):", 'info');
            foreach ($files as $file) {
                echo "  [DRY-RUN] {$file}\n";
            }
            return;
        }

        if (!$force) {
            foreach ($files as $file) {
                echo "  {$file}\n";
            }
            $this->log(" Hay {$count} archivo(s) por eliminar. Use --force para confirmar.", 'warning');
            return;
        }

        $deleted = 0;
        $failed  = 0;

        foreach ($files as $file) {
            if (unlink($file)) {
                $deleted++;
            } else {
                $failed++;
                $this->log("No se pudo eliminar: {$file}", 'error');
            }
        }

        $msg = "Eliminados: {$deleted}";
        if ($failed > 0) {
            $msg .= " | Fallidos: {$failed}";
        }

        $this->log($msg, $failed > 0 ? 'warning' : 'success');
    }
}
