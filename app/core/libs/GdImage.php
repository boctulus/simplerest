<?php

namespace simplerest\core\libs;

/*
    Utilities for GD lib

    By Pablo Bozzolo
*/
class GdImage
{
    protected $w;
    protected $h;
    protected $im;
    protected $foreground_color_name;
    protected $background_color_name;
    protected $colors = [];
    protected $shapes = [];
    protected $filter;

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

    function render(){
        if ($this->filter !== null){
            imagefilter($this->im, $this->filter);
        }

        // Enviar imagen al navegador
        header('Content-Type: image/png');
        imagepng($this->im);

        // Liberar memoria
        imagedestroy($this->im); 
    }

    function rectangle($x1, $y1, $width, $height, $color_name = null){
        if ($color_name == null){
            $color_name = $this->getForegroundColor();
        }

        $x2 = $x1 + $width;
        $y2 = $y1 + $height;

        imagerectangle($this->im, $x1, $y1, $x2, $y2, $this->colors[$color_name]);
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

    /*
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

}
