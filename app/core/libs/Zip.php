<?php

namespace simplerest\core\libs;

class Zip 
{
    /*
        https://stackoverflow.com/a/1334949/980631

        Modified by @boctulus

        Por concistencia si no esta presente la extension, intentar usar el comando zip
    */
    static function zip(string $ori, string $dst, ?Array $except = null, bool $overwrite = true)
    {
        if (!extension_loaded('zip') || !file_exists($ori)) {
            return false;
        }
    
        $zip = new \ZipArchive();
        if (!$zip->open($dst, $overwrite && file_exists($dst) ? \ZipArchive::OVERWRITE : \ZipArchive::CREATE)) {
            return false;
        }
    
        if (is_null($except)){
            $except = [];
        }

        $ori = str_replace('\\', '/', realpath($ori));
    
        if (is_dir($ori) === true)
        {
            $new_excluded = [];
            foreach ($except as $ix => $file){
                if (!Files::isAbsolutePath($file)){
                    $except[$ix] = Files::getAbsolutePath($file, $ori);
                }

                if (is_dir($except[$ix])){
                    $new_excluded = array_merge($new_excluded, Files::recursiveGlob($except[$ix] . '/*'));  
                }
            }

            $except = array_merge(array_values($except), array_values($new_excluded));

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($ori), \RecursiveIteratorIterator::SELF_FIRST);
    
            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);
    
                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;
    
                $file = realpath($file);
    
                if (!empty($except) && in_array($file, $except)){
                    continue;
                }

                if (is_dir($file) === true && !in_array($file, $except))
                {
                    $zip->addEmptyDir(str_replace($ori . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($ori . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($ori) === true)
        {
            $zip->addFromString(basename($ori), file_get_contents($ori));
        }
    
        return $zip->close();
    }

    protected static function isUnzipCommandAvailable() {
        // Verificar si el comando unzip está disponible en el sistema
        $output = [];
        $return_var = 0;
        exec('unzip -v', $output, $return_var);
        
        return $return_var === 0;
    }

    static function verifyUnzip(string $zipFile, string $destination)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === true) {
            $extractedFiles = [];

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $extractedFiles[] = $zip->getNameIndex($i);
            }

            $zip->close();

            foreach ($extractedFiles as $file) {
                $extractedFilePath = $destination . '/' . $file;
                if (!file_exists($extractedFilePath)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    //
    // Por concistencia implementar $overwrite 
    //
    public static function unzip(string $file_path, $destination = null, bool $verify = true) {
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
            if ($zip->open($file_path) !== true) {
                throw new \Exception("ZIP file was unable to be opened");                
            }

            // Extraer los archivos en la carpeta de destino
            $zip->extractTo($destination_folder);
            $zip->close();

            if ($verify && !static::verifyUnzip($file_path, $destination)){
                throw new \Exception("Uncompression failled or finish with errors");                    
            }
            
            // Retornar la ruta de la carpeta de destino
            return $destination_folder;
        }
        
        // Verificar si el comando unzip está disponible
        if (self::isUnzipCommandAvailable()) {
            // Ejecutar el comando unzip
            $command = "unzip $file_path -d $destination_folder";
            exec($command);

            if ($verify && !static::verifyUnzip($file_path, $destination)){
                throw new \Exception("Uncompression failled or finish with errors");                    
            }
            
            // Retornar la ruta de la carpeta de destino
            return $destination_folder;
        }
        
        // Lanzar excepción si ninguna opción está disponible
        throw new \Exception('No se puede descomprimir el archivo. El servicio no está disponible.');
    }

}


