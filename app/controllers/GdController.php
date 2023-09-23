<?php

namespace simplerest\controllers;

use simplerest\core\libs\GdImage;
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

        $colors = [ 
            'white' => [255,255,255],
            'black' => [0,0,0],
            'steelblue' => [70,130,180]
        ];
        
        //////////////////////////////////

        // Crear una nueva imagen
        $im = new GdImage($ancho, $alto);

        if ($color_inv){
            $im->invertColors();
        }
       
        // Create some colors
        foreach ($colors as $color_name => $color_value){
            $im->createColor($color_name, ...$color_value);
        }

        // Definir color de fondo
        $im->setBackgroundColor('white');

        // Defino color defecto de pincel
        #$im->setForegroundColor('steelblue');
        
        // Defino forma personalizada
        $im->setShape('row', function($cells_per_row, $x1, $y1, $w, $h, $color = null, $x_sp = 0) use($im) {
            if ($color == null){
                $color = $im->getForegroundColor();
            }

            foreach (range(0, $cells_per_row-1) as $c ){
                $x1 += $x_sp + $w;
                $im->rectangle($x1, $y1, $w, $h, $color);       
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

        // $im->setShape('col', function($row, int $n) use($im) {

        // });

        foreach (range(0,1) as $i){
            $im->row($boxes_per_row, $x, $y + ($h * $i), $w, $h, null, $x_sp);
        }

        // ...

        $im->render();                      
    }


   

}

