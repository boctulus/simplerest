<?php

namespace simplerest\core\libs;

class Zip 
{
    protected static function isUnzipCommandAvailable() {
        // Verificar si el comando unzip está disponible en el sistema
        $output = [];
        $return_var = 0;
        exec('unzip -v', $output, $return_var);
        
        return $return_var === 0;
    }

    static function unzip(string $file_path, $destination = null) {
        // Verificar si la extensión ZipArchive está disponible
        if (extension_loaded('zip')) {
            $zip = new \ZipArchive();
            
            // Abrir el archivo zip
            if ($zip->open($file_path) === true) {
                // Extraer los archivos en una carpeta temporal
                $temp_folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('unzip_');
                $zip->extractTo($temp_folder);
                $zip->close();
                
                // Retornar la ruta de la carpeta temporal
                return $temp_folder;
            }
        }
        
        // Verificar si el comando unzip está disponible
        if (self::isUnzipCommandAvailable()) {
            // Ejecutar el comando unzip
            $destination = sys_get_temp_dir() . '/' . uniqid('unzip_');
            $command = "unzip $file_path -d $destination";
            exec($command);
            
            // Retornar la ruta de la carpeta de destino
            return $destination;
        }
        
        // Lanzar excepción si ninguna opción está disponible
        throw new \Exception('No se puede descomprimir el archivo. El servicio no está disponible.');
    }
}


