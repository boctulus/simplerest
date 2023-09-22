<?php

namespace simplerest\core\libs;

/*
    Utilities for GD lib

    By Pablo Bozzolo
*/
class Image
{
    protected $w;
    protected $h;
    protected $im;

    function __construct($w, $h){
        $this->w = $w;
        $this->h = $h;

        // Crear una nueva imagen
        $this->im = imagecreatetruecolor($w, $h);
    }

    function getImage(){
        return $this->im;
    }

    function setBackgroundColor($color){
        imagefill($this->im, 0, 0, $color);
    }

    function render(){
        // Enviar imagen al navegador
        header('Content-Type: image/png');
        imagepng($this->im);

        // Liberar memoria
        // imagedestroy($this->im); 
    }

    function rectangle($x1, $y1, $width, $height, $color){
        $x2 = $x1 + $width;
        $y2 = $y1 + $height;

        imagerectangle($this->im, $x1, $y1, $x2, $y2, $color);
        return $this;
    }

}

