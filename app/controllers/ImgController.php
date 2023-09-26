<?php

namespace simplerest\controllers;

use simplerest\core\libs\Imaginator;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class ImgController extends MyController
{
    function __construct()
    {
        if (isset($_GET['debug'])){
            Imaginator::disable();
        }
    }
    
    function test(){
        ob_start();
        ?>

        <!-- Algun HTML -->    
        <h1>Probando GD</h1>
        
        <center>
            <img src="/img/render_01"/>
        </center>

        <?php

        $content = ob_get_clean();
        render($content);
    }

    function render_01()
    {
        $color_inv = true;

        // Definir dimensiones y colores
        $ancho = 800;
        $alto  = 1280;

        $colors = [ 
            'white' => [255,255,255],
            'black' => [0,0,0],
            'steelblue' => [70,130,180]
        ];
        
        $row_count     = 10;
        $boxes_per_row = 20;


        if ($row_count > 25){
            $alto *= ($row_count/25); 
        }

        if ($boxes_per_row > 22){
            $ancho *= ($boxes_per_row/22);
        }

        $max_row_w = $ancho * 0.9;
        $margin_r  = max($ancho * 0.1, 150);


        //////////////////////////////////

        // Crear una nueva imagen
        $im = new Imaginator($ancho, $alto);

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


        $x = 1000;
        $y = 50;
        $w = 30;
        $h = 20;
   
        $interline     = ($alto - 150)/ ($row_count);  
        $x             = ($ancho - $margin_r) - ($w * $boxes_per_row);   

        /*
            Vertical lines
        */

        $x_end = ($ancho - $margin_r) + $w;
        $x_ini = $x_end - ($boxes_per_row * $w);
        $x_med = ($x_end + $x_ini) * 0.5;

        // duplas considerando que la primera linea y la ultima formarian otra
        $duos  = ($row_count-1);

        $y_dif = ($duos * $h) + ($interline * ($row_count-1)) - 1   - ($row_count -2)*$h;

        // Middle line
        $im->line($x_med, $y, 0, $y_dif, null, true);

        // Line at the right
        $im->line($x_end + 20, $y, 0, $y_dif);
   
        /*
            Rows
        */

        $multi = 1;
        for ($i=0; $i<$row_count; $i++){
            $multi = ($i==0 || $i == $row_count-1) ? 1 : 2;
            $im->multipleRow($multi, $boxes_per_row, $x, $y + $interline * $i, $w, $h);
        }


        // ...

        $im->render();                      
    }
    
   
    /*
        Voy a intentar simular "layers" -- no funciona
    */
    function render_02(){
        $im_1 = new Imaginator(1000, 1000);

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
        $im = new Imaginator(1000, 1000);

        $im->createColor('black', 0,0,0);
        $im->createColor('white', 255,255,255);
        $im->createColor('steelblue', 70,130,180);

        $im->setBackgroundColor('white');
        $im->invertColors();
       
        $im->text(50,50, "Pablo ama a Feli", null, 5);
        $im->text(450,500, "Pablo ama a Feli", null, ASSETS_PATH . 'fonts/Swiss 721 Light BT.ttf', 20, 90);
        

        $im->render();    
    }

    function render_51(){
        // Create a 300x100 image
        $im = imagecreatetruecolor(300, 100);
        $red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
        $black = imagecolorallocate($im, 0x00, 0x00, 0x00);

        // Make the background red
        imagefilledrectangle($im, 0, 0, 299, 99, $red);

        // Path to our ttf font file
        $font_file = ASSETS_PATH . 'fonts/Swiss 721 Light BT.ttf';

        // Draw the text 'PHP Manual' using font size 13
        imagefttext($im, 13, 0, 105, 55, $black, $font_file, 'PHP Manual');


        // Output image to the browser
        header('Content-Type: image/png');

        imagepng($im);
        imagedestroy($im);
    }

}

