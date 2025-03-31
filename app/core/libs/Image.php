<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;

class Image
{
    /**
     * Verifica si una imagen existe y es accesible
     */
    static function isValidImage($image_path) {
        if (filter_var($image_path, FILTER_VALIDATE_URL)) {
            $headers = @get_headers($image_path);
            return $headers && strpos($headers[0], '200') !== false;
        }
        return file_exists($image_path);
    }

    /**
     * Convierte una imagen WebP a JPG
     */
    static function convertWebpToJpg($webp_path, $quality = 90) {
        // Crear un nombre temporal para la imagen convertida
        $output_path = Files::tempDir() . uniqid() . '.jpg';

        // Cargar la imagen WebP
        if (function_exists('imagecreatefromwebp')) {
            $webp = imagecreatefromwebp($webp_path);
            if ($webp) {
                // Guardar como JPG
                imagejpeg($webp, $output_path, $quality);
                imagedestroy($webp);
                return $output_path;
            }
        }

        // Si la conversión falla, retornar imagen por defecto
        return Config::get('app.default_featured_img');
    }
}


