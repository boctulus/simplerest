<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\CustomTags;
use Boctulus\Simplerest\Core\Libs\CodeReducer; 
use Boctulus\Simplerest\Controllers\MyApiController;
use Boctulus\Simplerest\Core\exceptions\NotFileButDirectoryException;

class Prompts extends MyApiController
{
    static protected $soft_delete = true;
    static protected $connect_to = [];
    static protected $hidden = [];
    static protected $hide_in_response = false;
    static protected $include_binary_files = false;
    static protected $max_files = 100;
    static protected $max_size = 50000;
    static protected $parser = CodeReducer::class;

    function __construct()
    {        
        parent::__construct();
    }

    protected function shouldIncludeFile($file_path) {
        // Verificar si el archivo existe y no es un directorio
        if (!file_exists($file_path) || is_dir($file_path)) {
            return false;
        }
        
        // Verificar tamaño del archivo
        $file_size = filesize($file_path);
        if ($file_size > static::$max_size) {
            return false;
        }
        
        // Verificar si es binario (solo si no se permiten binarios)
        if (!static::$include_binary_files) {
            // Leer solo una muestra del archivo (primeros 1024 bytes)
            $sample = file_get_contents($file_path, false, null, 0, 1024);
            if (Strings::isBinaryString($sample)) {
                return false;
            }
        }
        
        return true;
    }

    protected function reduceFileContent($content, $allowed_functions, $interface_replacement) {
        $functionsToKeep = $allowed_functions ?? ['*'];
        
        $replace_with_interface = [];
        $interface_replacement_exclusion_list = [];
        
        if ($interface_replacement !== null) {
            $include = $interface_replacement['include'] ?? ['*'];
            $exclude = $interface_replacement['exclude'] ?? [];
            
            if (!is_array($include)) {
                throw new \Exception("interface_replacements.include must be an array");
            }
            if (!is_array($exclude)) {
                throw new \Exception("interface_replacement.exclude must be an array");
            }
            
            if ($include !== ['*']) {
                $replace_with_interface = $include;
            }
            $interface_replacement_exclusion_list = $exclude;
        }
        
        $parser_instance = new static::$parser();
        return $parser_instance->reduceCode(
            $content,
            $functionsToKeep,
            [],
            $replace_with_interface,
            $interface_replacement_exclusion_list
        );
    }

    protected function onPostingAfterCheck($id, array &$data)
    {        
        if (!empty($data['description'])){
            CustomTags::register('dir', function($params) {
                $path      = $params['path'] ?? '';
                $pattern   = $params['pattern'] ?? '*.*';
                $recursive = (bool) ($params['recursive'] ?? false);
                $exclude   = $params['exclude'] ?? null;
            
                if (!is_dir($path)) {
                    throw new \Exception("Invalid directory path: $path");
                }
            
                $files = $recursive 
                    ? Files::recursiveGlob($path . DIRECTORY_SEPARATOR . $pattern, 0, $exclude) 
                    : Files::glob($path, $pattern);
    
                $files = Strings::enclose($files);
            
                return '[ ' . implode(', ' .PHP_EOL, $files) . ' ]';
            });

            // Registro di un callback per il tag "android-dir".
            // Esempio di utilizzo: [android-dir root="D:\Android\pos\MyPOS" pattern="*.java|*.xml" recursive="true"]
            CustomTags::register('android', function($params) {
                // Otteniamo il percorso base dal parametro "root"
                $path = $params['root'] ?? $params['path'] ?? '';
                // Impostiamo il pattern di default
                $pattern = $params['pattern'] ?? '*.*';
                // Impostiamo il valore di recursive di default a true
                $recursive = isset($params['recursive']) ? (bool) $params['recursive'] : true;
                // Possibilità di escludere determinati file o directory
                $exclude = $params['exclude'] ?? null;

                if (!is_dir($path)) {
                    throw new \Exception("Percorso di directory non valido: $path");
                }

                // Otteniamo i file, usando recursiveGlob o glob in base al flag recursive
                $files = $recursive 
                    ? Files::recursiveGlob($path . DIRECTORY_SEPARATOR . $pattern, 0, $exclude) 
                    : Files::glob($path, $pattern);

                // Formattiamo l'array dei file
                $files = Strings::enclose($files);
                
                return '[ ' . implode(', ' . PHP_EOL, $files) . ' ]';
            });

            $data['description'] = CustomTags::replace($data['description']);
        }

        $data['content'] = [];
        $base_path = $data['base_path'] ?? null;
        $data['files'] = array_unique($data['files'], SORT_REGULAR);

        try {
            foreach ($data['files'] as $file_item) {    
                // dd($file_item, 'FILE ITEM'); //            
                if (is_string($file_item)) {
                    $path = $file_item;
                    $include = ['*.*'];
                    $exclude = [];
                    $allowed_functions = null;
                    $interface_replacement = null;
                } elseif (is_array($file_item) && isset($file_item['path'])) {
                    $path = $file_item['path'];
                    $include = $file_item['include'] ?? ['*.*'];
                    $exclude = $file_item['exclude'] ?? [];
                    $allowed_functions = $file_item['allowed_functions'] ?? null;
                    $interface_replacement = $file_item['interface_replacement'] ?? null;

                    if (!is_array($include)) {
                        throw new \Exception("include must be an array");
                    }
                    if (!is_array($exclude)) {
                        throw new \Exception("exclude must be an array");
                    }
                    if ($allowed_functions !== null && !is_array($allowed_functions)) {
                        throw new \Exception("allowed_functions must be an array");
                    }
                } else {
                    throw new \Exception("Invalid file item");
                }

                try {                    
                    $content = Files::getContent($path, $base_path);
                    $content = $this->reduceFileContent($content, $allowed_functions, $interface_replacement);                    

                    $data['content'][$path] = $content;
                } catch (NotFileButDirectoryException $e) {
                    if ($base_path !== null && !Files::isAbsolutePath($path)) {
                        $dir_path = Files::addTrailingSlash($base_path) . DIRECTORY_SEPARATOR . Files::removeFirstSlash($path);
                    } else {
                        $dir_path = $path;
                    }

                    $files = [];
                    foreach ($include as $pattern) {
                        $pattern_files = Files::recursiveGlob($dir_path . DIRECTORY_SEPARATOR . $pattern);
                        $files = array_merge($files, $pattern_files);
                    }
                    $files = array_unique($files);

                    foreach ($exclude as $exclude_pattern) {
                        $exclude_files = Files::recursiveGlob($dir_path . DIRECTORY_SEPARATOR . $exclude_pattern);
                        $files = array_diff($files, $exclude_files);
                    }

                    if (count($files) > static::$max_files) {
                        throw new \Exception("So many files to read");
                    }

                    // dd($files);

                    // Filtrar solo archivos, excluyendo directorios
                    // $files = array_filter($files, 'is_file');

                    foreach ($files as $file) {
                        // dd($file, 'FILE');

                        if (is_dir($file)){
                            continue;
                        }

                        if (!$this->shouldIncludeFile($file)) {
                            $data['ignored'][] = $file;
                            continue;
                        }

                        $content = Files::readOrFail($file);
                        $content = $this->reduceFileContent($content, $allowed_functions, $interface_replacement);
                        $data['content'][$file] = $content;
                        $data['included'][] = Files::replaceSlashes($file);
                    }
                }
            }
            // dd($data, 'DATA');
        } catch (\Exception $e) {
            error($e->getMessage(), 400);
            return;
        }
    }
}