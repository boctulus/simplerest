<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyApiController;
use simplerest\core\exceptions\NotFileButDirectoryException;
use simplerest\core\libs\Logger;
use simplerest\core\libs\PHPParser; // Importar PHPParser

class Prompts extends MyApiController
{
    static protected $soft_delete = true;
    static protected $connect_to = [];

    static protected $hidden = [];

    static protected $hide_in_response = false;

    function __construct()
    {
        parent::__construct();
    }

    protected function onPostingAfterCheck($id, array &$data)
    {
        // Máximo número de archivos a cargar de un directorio
        $max_files = 100;

        $data['content'] = [];
        $base_path = $data['base_path'] ?? null;

        // Eliminar duplicados en files, preservando la estructura (strings u objetos)
        $data['files'] = array_unique($data['files'], SORT_REGULAR);

        foreach ($data['files'] as $file_item) {
            // Determinar si es string o array (objeto)
            if (is_string($file_item)) {
                $path = $file_item;
                $allowed_functions = null;
            } elseif (is_array($file_item) && isset($file_item['path'])) {
                $path = $file_item['path'];
                $allowed_functions = $file_item['allowed_functions'] ?? null;
                // Validar que allowed_functions sea un array si está presente
                if ($allowed_functions !== null && !is_array($allowed_functions)) {
                    throw new \Exception("allowed_functions must be an array");
                }
            } else {
                throw new \Exception("Invalid file item");
            }

            try {
                // Intentar leer el contenido del archivo
                $content = Files::getContent($path, $base_path);
                if ($allowed_functions !== null) {
                    $parser = new PHPParser();
                    $content = $parser->reduceCode($content, $allowed_functions);
                }
                $data['content'][$path] = $content;
            } catch (NotFileButDirectoryException $e) {
                // Manejar directorios
                if ($base_path !== null && !Files::isAbsolutePath($path)) {
                    $dir_path = Files::addTrailingSlash($base_path) . DIRECTORY_SEPARATOR . Files::removeFirstSlash($path);
                } else {
                    $dir_path = $path;
                }

                $files = Files::recursiveGlob($dir_path . DIRECTORY_SEPARATOR . '*.*');
                if (count($files) > $max_files) {
                    throw new \Exception("So many files to read");
                }

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($allowed_functions !== null) {
                        $parser = new PHPParser();
                        $content = $parser->reduceCode($content, $allowed_functions);
                    }
                    $data['content'][$file] = $content;
                }
            }
        }
    }
}