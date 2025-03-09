<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\CodeReducer;
use simplerest\core\libs\PHPParser; 
use simplerest\controllers\MyApiController;
use simplerest\core\exceptions\NotFileButDirectoryException;

class Prompts extends MyApiController
{
    static protected $soft_delete = true;
    static protected $connect_to = [];
    static protected $hidden = [];
    static protected $hide_in_response = false;
    static protected $include_binary_files = false;
    static protected $max_size = 50000;
    static protected $parser = CodeReducer::class;

    function __construct()
    {        
        parent::__construct();
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
        $max_files = 100;
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
                    // dd($base_path, 'BASE PATH'); //

                    $content = Files::getContent($path, $base_path);
                    // dd($content, 'CONTENT BEFORE'); //

                    $content = $this->reduceFileContent($content, $allowed_functions, $interface_replacement);
                    // dd($content, 'CONTENT REDUCED'); //

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

                    if (count($files) > $max_files) {
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

                        $content = Files::readOrFail($file);
                        $content = $this->reduceFileContent($content, $allowed_functions, $interface_replacement);
                        $data['content'][$file] = $content;
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