<?php

namespace Boctulus\Simplerest\Commands;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

class FileCommand implements ICommand 
{
	use CommandTrait;

	/**
     * Lista archivos y/o directorios según las opciones proporcionadas.
     *
     * @param string $dir Directorio a listar.
     * @param mixed ...$options Opciones como --pattern, --recursive, --include-dirs, --only-dirs.
     */
    /**
     * Lista archivos y/o directorios según las opciones proporcionadas.
     *
     * @param string $dir Directorio a listar.
     * @param mixed ...$options Opciones como --pattern, --recursive, --include-dirs, --only-dirs.
     */
    public function list(string $dir, ...$options) {
        // Parsear las opciones
        $opts = $this->parseOptions($options);
    
        // Normalizar la ruta
        $dir = Files::addTrailingSlash($dir);
    
        // Verificar que el directorio exista
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("El directorio '$dir' no existe o no es válido.");
        }
    
        // Obtener las entradas según las opciones
        if ($opts['recursive']) {
            if ($opts['include_dirs'] || $opts['only_dirs']) {
                // Usar deepScan para obtener archivos y directorios recursivamente
                $entries = Files::deepScan($dir, false); // false incluye directorios
            } else {
                // Usar recursiveGlob para listar solo archivos
                $entries = Files::recursiveGlob($dir . $opts['pattern'], 0, $opts['exclude']);
            }
        } else {
            if ($opts['include_dirs'] || $opts['only_dirs']) {
                // Usar scandir para listar el directorio actual (archivos y directorios)
                $entries = scandir($dir);
                $entries = array_filter($entries, function($entry) use ($dir) {
                    return $entry !== '.' && $entry !== '..';
                });
                $entries = array_map(function($entry) use ($dir) {
                    return $dir . $entry;
                }, $entries);
            } else {
                // Usar glob para listar solo archivos
                $entries = Files::glob($dir, $opts['pattern'], 0, $opts['exclude']);
            }
        }
    
        // Filtrar las entradas según las opciones
        $filtered = [];
        foreach ($entries as $entry) {
            $isDir = is_dir($entry);
    
            // Si es --only-dirs, saltar todo lo que no sea directorio
            if ($opts['only_dirs'] && !$isDir) {
                continue;
            }
    
            // Si no se usa --include-dirs ni --only-dirs, saltar directorios
            if (!$opts['include_dirs'] && !$opts['only_dirs'] && $isDir) {
                continue;
            }
    
            // Agregar la entrada filtrada con barras normalizadas
            $filtered[] = Files::convertSlashes($entry);
        }
    
        // Mostrar los resultados
        foreach ($filtered as $file) {
            echo $file . PHP_EOL;
        }
    }

    /**
     * Parsea las opciones pasadas al comando.
     *
     * @param array $args Argumentos/options del comando.
     * @return array Opciones parseadas.
     */
    protected function parseOptions(array $args): array {
        $options = [
            'pattern'   => '*.*',
            'exclude'   => null,
            'recursive' => false,
            'include_dirs' => false,
            'only_dirs' => false,
        ];

        foreach ($args as $arg) {
            if (preg_match('/^--(pattern)[=|:]([a-z0-9A-ZñÑ_\.-_\*\|]+)$/', $arg, $matches)){
                $options['pattern'] = $matches[2];
            } elseif (preg_match('/^(--recursive|-r)$/', $arg)){
                $options['recursive'] = true;
            } elseif ($arg === '--include-dirs') {
                $options['include_dirs'] = true;
            } elseif ($arg === '--only-dirs') {
                $options['only_dirs'] = true;
            } elseif (preg_match('/^--(exclude)[=|:]([\:a-z0-9A-ZñÑ_\.\*-_\/\\\\]+)$/', $arg, $matches)){
                $options['exclude'] = $matches[2];
            }
        }

        // dd($options, 'options');  

        return $options;
    }

    function help($name = null, ...$args) {
        echo "Use: php com file list <dir> [options]\n\n";
        echo "Options:\n";
        echo "  --pattern=<pattern>  Filter by pattern (e.g., '*.php')\n";
        echo "  --recursive          Search recursively in subdirectories\n";
        echo "  --include-dirs       Include directories in the list\n";
        echo "  --only-dirs          List only directories\n\n";
    
        echo <<<STR
        Examples:
        
        php com file list D:\laragon\www\simplerest\packages\juan-pepito\some\
        php com file list D:\laragon\www\simplerest\packages\juan-pepito\some\ --recursive
        php com file list D:\laragon\www\simplerest\packages\juan-pepito\some\ --include-dirs --recursive
        php com file list D:\laragon\www\simplerest\packages\juan-pepito\some\ --only-dirs --recursive 
        php com file list D:\laragon\www\simplerest\packages\juan-pepito\some\ --recursive --pattern='*.json'

        php com file list D:\Android\pos\MyPOS
        php com file list D:\Android\pos\MyPOS --pattern='*.java' --recursive
        php com file list D:\Android\pos\MyPOS --pattern='*.java|*.jar' --recursive
        php com file list '.' --pattern='*.bat'
        php com file list '.' --pattern='*.bat'  --recursive		
        php com file list D:\Android\pos\MyPOS --recursive --pattern='*.java|*.xml|*.gradle|*.properties' --exclude='D:\Android\pos\MyPOS\app\build\*'

        Restrictions:

        Combinations of --include-dirs with --pattern= will not work.

        Example:

        php com file list C:\Users\jayso\AndroidStudioProjects\FriendlyPOS --pattern='*.log'  --include-dirs
        STR;
    }

    
}



