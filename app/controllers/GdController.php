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
        
        /*
            Defino formas personalizadas
        */

        $im->setShape('row', function($cells_per_row, $x1, $y1, $w, $h, $color = null, bool $filled = false, $x_sp) use($im) {
            if ($color == null){
                $color = $im->getForegroundColor();
            }

            foreach (range(0, $cells_per_row-1) as $c ){
                $x1 += $x_sp + $w;
                $im->rectangle($x1, $y1, $w, $h, $color, $filled);       
            }
        });


        /*
            Ej:

            $im->multipleRow(2, $boxes_per_row, $x, $y + 200, $w, $h, null, true, 2, 2);
        */
        $im->setShape('multipleRow', function($n, $cells_per_row, $x1, $y1, $w, $h, $color=null, bool $filled=false, $x_sp=0, $y_sp=0) use($im) {
            foreach (range(0,$n-1) as $i){
                $im->row($cells_per_row, $x1, $y1 + (($h + $y_sp) * $i), $w, $h, $color, $filled, $x_sp);
            }
        });        


        $boxes_per_row = 10;

        $x = 50;
        $y = 50;
        $w = 80;
        $h = 20;


        /*
            Ahora debo crear el arreglo de filas

            Necesito:

            - Saber los limites de la forma
            - Conocer el punto medio en X y en Y 
        */

   
        $im->multipleRow(1, $boxes_per_row, $x, $y,       $w, $h);

        $im->multipleRow(2, $boxes_per_row, $x, $y + 200, $w, $h);

        $im->multipleRow(1, $boxes_per_row, $x, $y + 400, $w, $h);


        // ...

        $im->render();                      
    }
   
    /*
        Voy a intentar simular "layers"
    */
    function render_02(){
        $im_1 = new GdImage(1000, 1000);

        $im_1->createColor('black', 0,0,0);
        $im_1->createColor('white', 255,255,255);
        $im_1->createColor('steelblue', 70,130,180);

        $im_1->setBackgroundColor('white');
        $im_1->invertColors();

        $im_1->line(0, 0, 200, 200, 'steelblue');
        $im_1->rectangle(50, 50, 100, 30, null, true);  // si se dibuja despues queda "arriba"       

        $im_1->render();
                    
    }


    function render_50(){
        $redimg = imagecreatetruecolor(100, 100);
        $image = imagecreatefrompng('image.png');

        // sets background to red
        $red = imagecolorallocate($redimg, 255, 0, 0);
        imagefill($redimg, 0, 0, $red);

        // Merge the red image onto the PNG image
        imagecopymerge($image, $redimg, 0, 0, 0, 0, 100, 100, 75);

        header('Content-type: image/png');
        imagepng($image);
        imagedestroy($image);
        imagedestroy($redimg);
    }

}

