<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\Files;

class FileListCommand extends BaseCommand
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
        // El directorio puede venir como primer arg posicional o como --dir
        $dir = $parsed['_positional'][0] ?? $this->opt($parsed, 'dir', '.');

        $pattern     = $this->opt($parsed, 'pattern', '*.*');
        $exclude     = $this->opt($parsed, 'exclude');
        $recursive   = $this->opt($parsed, 'recursive', false);
        $includeDirs = $this->opt($parsed, 'include_dirs', false);
        $onlyDirs    = $this->opt($parsed, 'only_dirs', false);

        $dir = Files::addTrailingSlash($dir);

        if (!is_dir($dir)) {
            echo "✗ El directorio '{$dir}' no existe.\n";
            return;
        }

        if ($recursive) {
            if ($includeDirs || $onlyDirs) {
                $entries = Files::deepScan($dir, false);
            } else {
                $entries = Files::recursiveGlob($dir . $pattern, 0, $exclude);
            }
        } else {
            if ($includeDirs || $onlyDirs) {
                $raw     = scandir($dir);
                $entries = array_map(
                    fn($e) => $dir . $e,
                    array_filter($raw, fn($e) => $e !== '.' && $e !== '..')
                );
            } else {
                $entries = Files::glob($dir, $pattern, 0, $exclude);
            }
        }

        $filtered = [];
        foreach ($entries as $entry) {
            $isDir = is_dir($entry);
            if ($onlyDirs && !$isDir) continue;
            if (!$includeDirs && !$onlyDirs && $isDir) continue;
            $filtered[] = Files::convertSlashes($entry);
        }

        foreach ($filtered as $file) {
            echo $file . PHP_EOL;
        }

        echo "\n" . count($filtered) . " entrada(s).\n";
    }
}
