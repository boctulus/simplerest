<?php

namespace simplerest\core\libs;

/*
    Utilities for GD lib

    By Pablo Bozzolo
*/
class Imaginator
{
    protected $w;
    protected $h;
    protected $im;
    protected $image_format;
    protected $foreground_color_name;
    protected $background_color_name;
    protected $colors = [];
    protected $shapes = [];
    protected $filter;

    protected static $rendering = true;

    static function disable(){
        static::$rendering = false;
    }

    function __construct($w, $h){
        $this->w = $w;
        $this->h = $h;

        $this->im = imagecreatetruecolor($w, $h);
    }

    function getImage(){
        return $this->im;
    }

    function createColor($name, $r, $g, $b){
        $this->colors[$name] = imagecolorallocate($this->im, $r, $g, $b);; 
    }

    protected function __color($color){
        if (is_string($color)){
            $color = $this->colors[$color];
        }

        return $color;
    }

    function setForegroundColor(string $color_name){
        $this->foreground_color_name = $color_name;
    }

    /*
        Devuelve el foreground color por defecto
        o el primer color que encuentra distinto del de background
    */
    function getForegroundColor(){
        if ($this->foreground_color_name != null){
            return $this->foreground_color_name;
        }

        foreach (array_keys($this->colors) as $color_name){
            if ($color_name != $this->background_color_name){
                $this->foreground_color_name = $color_name;                
                return $color_name;
            }
        }           
    }

    function setBackgroundColor($color){
        $this->background_color_name = $color;
        imagefill($this->im, 0, 0, $this->__color($color));
    }

    function invertColors(){
        // Aplicar el filtro para invertir colores
        $this->filter = IMG_FILTER_NEGATE;
    }

    function render($image_format = 'png'){
        if (!in_array($image_format, ['png', 'gif', 'jpeg'])){
            throw new \InvalidArgumentException("Invalid image file format");
        }

        if ($this->filter !== null){
            imagefilter($this->im, $this->filter);
        }

        $image_format = $this->image_format ?? $image_format;

        if (static::$rendering === false){
            return;
        }

        $fn = "image{$image_format}";

        // Enviar imagen al navegador
        header('Content-Type: image/' . $image_format);
        $fn($this->im);

        // Liberar memoria
        imagedestroy($this->im); 
    }

    function rectangleTo($x1, $y1, $x2, $y2, $color_name = null, bool $filled = false){
        if ($color_name == null){
            $color_name = $this->getForegroundColor();
        }

        $fn = $filled ? 'imagefilledrectangle' : 'imagerectangle';

        $fn($this->im, $x1, $y1, $x2, $y2, $this->colors[$color_name]);
        return $this;
    }

    function rectangle($x1, $y1, $width, $height, $color_name = null, bool $filled = false){
        if ($color_name == null){
            $color_name = $this->getForegroundColor();
        }

        $x2 = $x1 + $width;
        $y2 = $y1 + $height;

        $this->rectangleTo($x1, $y1, $x2, $y2, $color_name, $filled);
        return $this;
    }

    function lineTo(int $x1, int $y1, int $x2, int $y2, $color_name = null){
        if ($color_name == null){
            $color_name = $this->getForegroundColor();
        }

        imageline($this->im, $x1, $y1, $x2, $y2, $this->colors[$color_name]);
        return $this;
    }

    function line(int $x1, int $y1, int $delta_x, int $delta_y, $color_name = null){
        if ($color_name == null){
            $color_name = $this->getForegroundColor();
        }

        $x2 = $x1 + $delta_x;
        $y2 = $y1 + $delta_y;

        $this->lineTo($x1, $y1, $x2, $y2, $color_name);
        return $this;
    }

    // Mas formas nativas como arc(), etc
    // ...

    /*
        Seteo forma personalizada
    */
    function setShape(string $name, callable $callback){
        $this->shapes[$name] = $callback;
    }

    /*PDOC
        Dibuja forma personalizada
    */
    function shape($name, ...$args){
        $this->shapes[$name](...$args);
    }

    /*
        Recupera forma, quizas color
    */
    function __call($name, $args){
        $this->shapes[$name](...$args);
    }

    function copyFrom(Imaginator $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height){
        imagecopy($this->im, $src_image->getImage(), $dst_x, $dst_y, $src_x, $src_y, $src_width, $src_height);
    }

    function copyTo($dst_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height){
        imagecopy($dst_image->getImage(), $this->im, $dst_x, $dst_y, $src_x, $src_y, $src_width, $src_height);
    }

    function mergeFrom(Imaginator $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height, int $pct){
        imagecopymerge($this->im, $src_image->getImage(), $dst_x, $dst_y, $src_x, $src_y, $src_width, $src_height, $pct);
    }

    function mergeTo($dst_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height, int $pct){
        imagecopymerge($dst_image->getImage(), $this->im, $dst_x, $dst_y, $src_x, $src_y, $src_width, $src_height, $pct);
    }

}

