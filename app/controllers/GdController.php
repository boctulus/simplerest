<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class GdController extends MyController
{
    function test(){
        ob_start();
        ?>

        <!-- Algun HTML -->    
        <h1>Probando GD</h1>
        
        <img src="/gd/render_00"/>

        <?php

        $content = ob_get_clean();
        render($content);
    }

    function render_00()
    {
        $color_inv = true;

        // Definir dimensiones y colores
        $ancho = 1780;
        $alto  = 1280;

        // helper
        function rectangle($x1, $y1, $width, $height, $image, $color){
            $x2 = $x1 + $width;
            $y2 = $y1 + $height;

            imagerectangle($image, $x1, $y1, $x2, $y2, $color);
        }
        
        // Crear una nueva imagen
        $im = imagecreatetruecolor($ancho, $alto);

        $c_blk = [255,255,255];
        $c_wht = [0,0,0]; 

        if ($color_inv){
            $c_blk = [0,0,0];
            $c_wht = [255,255,255]; 
        }

        // Create some colors
        $white = imagecolorallocate($im, ...$c_blk);
        $black = imagecolorallocate($im, ...$c_wht);

        // Definir color de fondo
        imagefill($im, 0, 0, $white);
        
        $x1 = 50;
        $y1 = 50;
        $w  = 80;
        $h  = 20;
        
        $x_sp = 5;

        foreach (range(0,5) as $c ){
            $x1 += $x_sp + $w;
            rectangle($x1, $y1, $w, $h, $im, $black);       
        }


        // Dibujar las líneas verticales
        // for ($i = 0; $i < 10; $i++) {
        //     $x = $i * 40;
        //     imageline($im, $x, 0, $x, $alto, $black);
        // }

        // // Dibujar las líneas horizontales
        // for ($i = 0; $i < 5; $i++) {
        //     $y = $i * 40;
        //     imageline($im, 0, $y, $ancho, $y, $black);
        // }

        // // Dibujar líneas adicionales
        // imageline($im, 200, 0, 200, $alto, $black);
        // imageline($im, 0, 100, $ancho, 100, $black);

        // Enviar imagen al navegador
        header('Content-Type: image/png');
        imagepng($im);

        // Liberar memoria
        imagedestroy($im);                       
    }

    function render_01(){
        // Set the content-type
        header('Content-Type: image/png');

        // Create the image
        $im = imagecreatetruecolor(400, 30);

        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 399, 29, $black);

        // The text to draw
        $text = 'Testing';
        // Replace path by your own font path
        $font = 'verdana.ttf';

        // Add the text

        $bbox = imagettfbbox(20, 0, $font, $text);

        $x = $bbox[1] + (imagesx($im) / 2) - ($bbox[4]);
        $y = $bbox[3] + (imagesy($im) / 2) - ($bbox[5]);

        imagerectangle($im, 0, 0, $x, $y, $white);
        imagettftext($im, 20, 0, 0, 20, $white, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        imagejpeg($im);
        imagedestroy($im);
    }

}

