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
    protected $colors = [];
    protected $shapes = [];

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

    function getColor($color){
        if (is_string($color)){
            $color = $this->colors[$color];
        }

        return $color;
    }

    function setBackgroundColor($color){
        imagefill($this->im, 0, 0, $this->getColor($color));
    }

    function render(){
        // Enviar imagen al navegador
        header('Content-Type: image/png');
        imagepng($this->im);

        // Liberar memoria
        imagedestroy($this->im); 
    }

    function rectangle($x1, $y1, $width, $height, $color_name){
        $x2 = $x1 + $width;
        $y2 = $y1 + $height;

        imagerectangle($this->im, $x1, $y1, $x2, $y2, $this->colors[$color_name]);
        return $this;
    }

    function setShape(string $name, callable $callback){
        $this->shapes[$name] = $callback;
    }

    function shape($name, ...$args){
        $this->shapes[$name](...$args);
    }

    function __call($name, $args){
        $this->shapes[$name](...$args);
    }

}

