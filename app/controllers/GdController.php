<?php

namespace simplerest\controllers;

use simplerest\core\libs\Image;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class GdController extends MyController
{
    function debug(){
        $this->render_01();
    }

    function test(){
        ob_start();
        ?>

        <!-- Algun HTML -->    
        <h1>Probando GD</h1>
        
        <img src="/gd/render_01"/>

        <?php

        $content = ob_get_clean();
        render($content);
    }

    function render_01()
    {
        $color_inv = true;

        // Definir dimensiones y colores
        $ancho = 1780;
        $alto  = 1280;
        
        // Crear una nueva imagen
        $im = new Image($ancho, $alto);

        $c_blk = [255,255,255];
        $c_wht = [0,0,0]; 

        if ($color_inv){
            $c_blk = [0,0,0];
            $c_wht = [255,255,255]; 
        }

        // Create some colors
        $white = $im->createColor('white', ...$c_blk);
        $black = $im->createColor('black', ...$c_wht);

        // Definir color de fondo
        $im->setBackgroundColor('white');
        
        // Defino forma personalizada
        $im->setShape('row', function($cells_per_row, $x1, $y1, $w, $h, $x_sp = 0) use($im) {
            foreach (range(0, $cells_per_row-1) as $c ){
                $x1 += $x_sp + $w;
                $im->rectangle($x1, $y1, $w, $h, 'black');       
            }
        });

        $boxes_per_row = 10;

        $x = 50;
        $y = 50;
        $w = 80;
        $h = 20;
        
        $x_sp = 5;

        /*
            Ahora debo crear el arreglo de filas
        */
        $im->row($boxes_per_row, $x, $y, $w, $h, $x_sp);

        $im->render();                      
    }


   

}

