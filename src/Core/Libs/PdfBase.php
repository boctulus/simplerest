<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

use Fpdf\Fpdf;

abstract class PdfBase extends Fpdf {
    const MODE_DOWNLOAD = 'DOWNLOAD';
    const MODE_NEW_TAB  = 'TAB';

    static protected $pageWidth  = 210;    // A4 width
    static protected $pageHeight = 297;    // A4 height
    protected $data;
    protected $filename;
    protected $storage_path;
    protected $mode = 'DOWNLOAD';
    protected $default_font = ['Helvetica', 10];

    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $size = 'A4'
    ) {
        if (!empty($size)){
            if (is_array($size)){
                list(static::$pageWidth, static::$pageHeight) = $size;
            } 
        } else {
            $unit = 'mm';
            $size = [ static::$pageWidth, static::$pageHeight ];
        }

        parent::__construct($orientation, $unit, $size);

        $this->SetFont($this->default_font[0], '', $this->default_font[1]);
        
        $this->SetAutoPageBreak(true, 20);

        $this->setStorage(ETC_PATH . DIRECTORY_SEPARATOR . 'pdfs');
    }

    // Lanzo Exception a fin de poder capturarla
    function Error($msg)
    {        
        throw new \Exception($msg);
    }

    function setData(array $product){
        $this->data = $product;
        return $this;
    }

    function setMode($mode){
        $this->mode = $mode;
        return $this;
    }

    // Set temporary storage path 
    function setStorage($path){
        $this->storage_path = Files::trimTrailingSlash($path) . DIRECTORY_SEPARATOR;

        if (!file_exists($this->storage_path)){
            Files::mkDirOrFail($this->storage_path);
        }

        return $this;
    }

    function setFilename($name){
        if (!Strings::endsWith('.pdf', $name)){
            $name .= '.pdf';
        }

        $this->filename = $name;
        return $this;
    }

    function show(){
        // Asegurar que no haya output previo
        if (ob_get_length()){
            ob_clean();
        } 

        $filepath = $this->storage_path . $this->filename;

        // Ahora enviamos el archivo al navegador
        if (!file_exists($filepath)) {
            throw new \Exception("El PDF no se pudo generar o guardar");
        }

        header('Content-Type: application/pdf');
        header('Content-Type: application/pdf; charset=utf-8');
        
        // Para descarga:
        if ($this->mode == static::MODE_DOWNLOAD){
            header('Content-Disposition: attachment; filename="' . $this->filename . '"');
        } else 
            // O para abrir en nueva pestaña:
            if ($this->mode == static::MODE_NEW_TAB){
                header('Content-Disposition: inline; filename="' . $this->filename . '"');
            }
        
        header('Cache-Control: public, max-age=0');
        header('Content-Length: ' . filesize($filepath));
        header('Content-Transfer-Encoding: binary');
    
        flush();
        
        readfile($filepath);
    }

    function prepareDownload($filename = null){
        if (!empty($filename)){
            $this->setFilename($filename);
        }

        $filepath = $this->storage_path . $this->filename;

        // ... todo tu código de generación del PDF ...
        $this->Output('F', $filepath); // 'F' significa guardar a archivo

        $this->show();

        // Limpiamos el archivo temporal
        unlink($filepath);
        exit;
    }
    
    // function Header() {}
    
    // function Footer() {}

    function render(){}

    /*
        Imagenes
    */

    /**
     * Procesa y valida una imagen antes de agregarla al PDF
     * 
     * @param string $image_path URL o ruta de la imagen
     * @return string|null Ruta de la imagen procesada o null si no es válida
     */
    protected function processImage($image_path) {
        // Si la imagen no existe o la URL no es válida
        if (empty($image_path) || !Image::isValidImage($image_path)) {
            return Config::get('app.default_featured_img');
        }

        // Obtener información de la imagen
        $image_info = @getimagesize($image_path);
        if ($image_info === false) {
            return Config::get('app.default_featured_img');
        }

        $extension = image_type_to_extension($image_info[2], false);

        // Si es webp, convertir a PNG
        if ($extension === 'webp') {
            return Image::convertWebpToJpg($image_path);
        }

        // Si es una imagen válida y soportada por FPDF
        if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            return $image_path;
        }

        return Config::get('app.default_featured_img');
    }    

    /**
     * Método wrapper para agregar imágenes al PDF de forma segura
     */
    function Image($image_path, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '') {
        try {
            $processed_image = $this->processImage($image_path);
            if ($processed_image) {
                parent::Image($processed_image, $x, $y, $w, $h, $type, $link);
                
                // Si la imagen fue convertida (temporal), la eliminamos
                if (strpos($processed_image, Files::tempDir()) === 0) {
                    @unlink($processed_image);
                }
            }
        } catch (\Exception $e) {
            // Si algo falla, usamos la imagen por defecto
            parent::Image(Config::get('app.default_featured_img'), $x, $y, $w, $h);
        }
    }
}

