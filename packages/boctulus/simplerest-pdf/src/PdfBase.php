<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;
use FPDF;

abstract class PdfBase extends Fpdf
{
    const MODE_DOWNLOAD = 'DOWNLOAD';
    const MODE_NEW_TAB  = 'TAB';

    static protected $pageWidth  = 210;
    static protected $pageHeight = 297;
    protected $data;
    protected $filename;
    protected $storage_path;
    protected $mode = 'DOWNLOAD';
    protected $default_font = ['Helvetica', 10];

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        if (!empty($size)) {
            if (is_array($size)) {
                list(static::$pageWidth, static::$pageHeight) = $size;
            }
        } else {
            $unit = 'mm';
            $size = [static::$pageWidth, static::$pageHeight];
        }

        parent::__construct($orientation, $unit, $size);
        $this->SetFont($this->default_font[0], '', $this->default_font[1]);
        $this->SetAutoPageBreak(true, 20);
        $this->setStorage(ETC_PATH . DIRECTORY_SEPARATOR . 'pdfs');
    }

    function Error($msg)
    {
        throw new \Exception($msg);
    }

    function setData(array $product)
    {
        $this->data = $product;
        return $this;
    }

    function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    function setStorage($path)
    {
        $this->storage_path = Files::trimTrailingSlash($path) . DIRECTORY_SEPARATOR;

        if (!file_exists($this->storage_path)) {
            Files::mkDirOrFail($this->storage_path);
        }

        return $this;
    }

    function setFilename($name)
    {
        if (!Strings::endsWith('.pdf', $name)) {
            $name .= '.pdf';
        }

        $this->filename = $name;
        return $this;
    }

    function show()
    {
        if (ob_get_length()) {
            ob_clean();
        }

        $filepath = $this->storage_path . $this->filename;
        if (!file_exists($filepath)) {
            throw new \Exception('El PDF no se pudo generar o guardar');
        }

        header('Content-Type: application/pdf');
        header('Content-Type: application/pdf; charset=utf-8');
        if ($this->mode == static::MODE_DOWNLOAD) {
            header('Content-Disposition: attachment; filename="' . $this->filename . '"');
        } elseif ($this->mode == static::MODE_NEW_TAB) {
            header('Content-Disposition: inline; filename="' . $this->filename . '"');
        }

        header('Cache-Control: public, max-age=0');
        header('Content-Length: ' . filesize($filepath));
        header('Content-Transfer-Encoding: binary');
        flush();
        readfile($filepath);
    }

    function prepareDownload($filename = null)
    {
        if (!empty($filename)) {
            $this->setFilename($filename);
        }

        $filepath = $this->storage_path . $this->filename;
        $this->Output('F', $filepath);
        $this->show();
        unlink($filepath);
        exit;
    }

    function render() {}

    protected function processImage($image_path)
    {
        if (empty($image_path) || !Image::isValidImage($image_path)) {
            return Config::get('app.default_featured_img');
        }

        $image_info = @getimagesize($image_path);
        if ($image_info === false) {
            return Config::get('app.default_featured_img');
        }

        $extension = image_type_to_extension($image_info[2], false);
        if ($extension === 'webp') {
            return Image::convertWebpToJpg($image_path);
        }

        if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            return $image_path;
        }

        return Config::get('app.default_featured_img');
    }

    function Image($image_path, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '')
    {
        try {
            $processed_image = $this->processImage($image_path);
            if ($processed_image) {
                parent::Image($processed_image, $x, $y, $w, $h, $type, $link);
                if (strpos($processed_image, Files::tempDir()) === 0) {
                    @unlink($processed_image);
                }
            }
        } catch (\Exception $e) {
            parent::Image(Config::get('app.default_featured_img'), $x, $y, $w, $h);
        }
    }
}
