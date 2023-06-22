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

    public static function unzip(string $file_path, $destination = null) {
        // Utilizar la ruta de destino si se proporciona
        if ($destination !== null) {
            $destination_folder = rtrim($destination, '/');
        } else {
            $destination_folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('unzip_');
        }

        // Verificar si la extensión ZipArchive está disponible
        if (extension_loaded('zip')) {
            $zip = new \ZipArchive();
            
            // Abrir el archivo zip
            if ($zip->open($file_path) === true) {
                // Extraer los archivos en la carpeta de destino
                $zip->extractTo($destination_folder);
                $zip->close();
                
                // Retornar la ruta de la carpeta de destino
                return $destination_folder;
            }
        }
        
        // Verificar si el comando unzip está disponible
        if (self::isUnzipCommandAvailable()) {
            // Ejecutar el comando unzip
            $command = "unzip $file_path -d $destination_folder";
            exec($command);
            
            // Retornar la ruta de la carpeta de destino
            return $destination_folder;
        }
        
        // Lanzar excepción si ninguna opción está disponible
        throw new \Exception('No se puede descomprimir el archivo. El servicio no está disponible.');
    }
}



