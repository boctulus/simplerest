<?php

use Boctulus\Simplerest\Core\Libs\Files;

require_once __DIR__ . '/BaseFileCommand.php';

class FileListCommand extends BaseFileCommand
{
    public function __construct()
    {
        $this->command     = 'list';
        $this->description = 'Lista archivos y/o directorios de un directorio';
        $this->aliases     = ['ls'];
        $this->examples    = [
            'php com file list D:\\laragon\\www\\simplerest',
            'php com file list . --pattern=*.php --recursive',
            'php com file list . --only-dirs --recursive',
            'php com file list . --include-dirs --recursive',
            'php com file list . --pattern=*.php --recursive --exclude=vendor\\*',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['pattern', 'exclude'],
            'flags'    => ['recursive', 'include-dirs', 'only-dirs'],
            'options'  => [
                'pattern'      => ['describe' => 'Filtro de patrón (ej: *.php, *.php|*.js)', 'default' => '*.*'],
                'exclude'      => ['describe' => 'Patrón a excluir'],
                'recursive'    => ['describe' => 'Buscar recursivamente'],
                'include-dirs' => ['describe' => 'Incluir directorios en el listado'],
                'only-dirs'    => ['describe' => 'Listar solo directorios'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $dir = $parsed['_positional'][0] ?? $this->opt($parsed, 'dir', '.');

        $filtered = $this->getFileEntries($parsed);

        if (empty($filtered) && !is_dir(Files::addTrailingSlash($dir))) {
            echo "✗ El directorio '{$dir}' no existe.\n";
            return;
        }

        foreach ($filtered as $file) {
            echo $file . PHP_EOL;
        }

        echo "\n" . count($filtered) . " entrada(s).\n";
    }
}
