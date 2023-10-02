<?php

namespace simplerest\controllers;

use simplerest\core\libs\Factory;
use simplerest\core\libs\Imaginator;
use simplerest\core\libs\Messurements as M;
use simplerest\controllers\MyController;
use simplerest\core\libs\StdOut;

class ImgController extends MyController
{
    function __construct()
    {
        if (isset($_GET['debug'])){
            Imaginator::disable();
        } else {
            StdOut::hideResponse();
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
        // Step 1
        $upright_height = 120;  // inches

        $upright_depth  = 42;   // inches   
        $beam_length    = 96;   // inches *
        
        $beam_levels    = 2;
  
        // Step 3
        $h_feets        = 100;  // feet
        $w_feets        = 100;  // feet

        // Step 4
        $aisle          = M::toInches(5, 6); // es convertido a inches
                
        /*
            Voy a intentar calcular el row_count y boxes_per_row

            Parece ser que se reservan 1,5 feet de cada lado o sea se deben restar al height
            y si digamos el Aisle es de 5'6'' entonces boxes_per_row = 1 porque 10'' - 3'' = 7''
            y 7'' dividido 5'6'' da 1 y fraccion 
        */

        $upright_height_feets = floor($upright_height/12); 
        $upright_depth_feets  = floor($upright_depth/12);  
        $beam_length_feets    = floor($beam_length/12);  
        $aisle_feets          = round($aisle/12, 2);  

        $h                    = M::toInches($h_feets);  // inches
        $w                    = M::toInches($w_feets);  // inches

        // Calculo        

        //  StdOut::pprint($w_feets, 'width');
        //  StdOut::pprint($aisle_feets, 'aisle feets');

        $w_acc = $upright_depth;

        StdOut::pprint($h - $upright_depth, "Max");
        StdOut::pprint($w_acc, 'w acc');

        // 42 + 60 + 2*42 + 60 + 2*42 + 60 + 2*42 + 60 + 42

        $row_count = 1;
        while ($row_count<999999 && $w_acc < $w - $upright_depth - $aisle) {
            $w_acc += $aisle + ($upright_depth * 2);
            $row_count += 1;

            // StdOut::pprint("+= $aisle + ($upright_depth * 2)");
            // StdOut::pprint($w_acc, 'w acc');
            // StdOut::pprint($row_count, 'row count');
        }
    
        if ($w_acc < $w){
            $w_acc += $aisle + $upright_depth;
            $row_count++;
        }

        StdOut::pprint(M::toFeetAndInches($w_acc), 'w acc');
        StdOut::pprint("$row_count : row count");

        //  StdOut::pprint($h_feets, 'h');
        //  StdOut::pprint($aisle, 'aisle');
        //  StdOut::pprint$boxes_per_row, 'boxes per row');    

        $boxes_per_row        = floor($h / $beam_length);

    
        // exit;

        $color_inv = true;

        // Definir dimensiones y colores
        $ancho = 800;
        $alto  = 1280;

        $colors = [ 
            'white' => [255,255,255],
            'black' => [0,0,0],
            'steelblue' => [70,130,180]
        ];

        $font_1 = ASSETS_PATH . 'fonts/Swiss 721 Light BT.ttf';
        $font_2 = ASSETS_PATH . 'fonts/Swiss721BT-Light.otf';
        
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

        $y_dif = ($duos * $h) + ($interline * ($row_count-1)) - 1   - ($row_count -2) * $h -$h;

        // Middle line
        $im->line($x_med, $y, 0, $y_dif, null, true);

        // Line at the right
        $im->line($x_end + 20, $y, 0, $y_dif);
   
        /*
            Rows
        */

        $im->multipleRow(1, $boxes_per_row, $x, $y, $w, $h);
        $w_acc = $w;

        for ($i=1; $i<$row_count; $i++){
            $multi = ($i == $row_count-1) ? 1 : 2;
            $im->multipleRow($multi, $boxes_per_row, $x, $y + $interline * $i -$h, $w, $h);

            $w_acc += M::toInches($aisle) + ($w * $multi); 
        }

        /*
            Texts
        */

        // Suma
        $im->text($x_med - 12, $y - 6, M::toFeetAndInches($beam_length * $boxes_per_row),   null, $font_2, 15);

        // Numero que aparece arriba de la primera celda
        $im->text($x + $w + 2, $y + $w + 12, "$beam_length''",   null, $font_2, 15); 

        // Numero que aparece a la izquierda de la primera celda
        $im->text($x - 2, $y + $h  -3, "$upright_depth''"              , null, $font_2, 15);

        // Numero que aparece apaisado del lado derecho
        $im->text($x_end + 45, floor($y_dif / 2), $w_feets . "'",   null, $font_2, 15, 90);

        // Leyendas de los pasillos (aisle)
        $lbl = M::toFeetAndInches($aisle);
        for ($i=0; $i<$row_count -1; $i++){
            $im->text($x_med - 20 - strlen($lbl) * 6, $y + $interline * ($i+0.5) +  0.5 * $h , $lbl, null, $font_2, 15);
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

        $im->setBackgroundColor('white');
        $im->invertColors();
       
        $im->text(50,50, "Pablo ama a Feli");

        $font_1 = ASSETS_PATH . 'fonts/Swiss 721 Light BT.ttf';
        $font_2 = ASSETS_PATH . 'fonts/Swiss721BT-Light.otf';

        $im->text(450,500, "Pablo ama a Feli", null, $font_1, 20);
        $im->text(450,600, "Pablo ama a Feli", null, $font_2, 20);

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

    function render_pie(){
        $im = new Imaginator(400, 400);

        $im->createColor('black', 0,0,0);
        $im->createColor('white', 255,255,255);
        $im->createColor('azul', 52, 152, 219);
        $im->createColor('rojo', 231, 76, 60);  
        $im->createColor('transparente', 0, 0, 0, 127);      

        $im->setBackgroundColor('transparente');
        // $im->invertColors();
       
        // $im->text(50,50, "leyenda");

        // $font_1 = ASSETS_PATH . 'fonts/Swiss 721 Light BT.ttf';
        // $font_2 = ASSETS_PATH . 'fonts/Swiss721BT-Light.otf';

        $cantidadActivos = 66; // Cantidad de activos
        $cantidadInactivos = 11; // Cantidad de inactivos

        $im->arcPie(200, 200, 300, 300, 0, (360 * $cantidadActivos / ($cantidadActivos + $cantidadInactivos)), 'rojo');
        $im->arcPie(200, 200, 300, 300, (360 * $cantidadActivos / ($cantidadActivos + $cantidadInactivos)), 360, 'azul');

        $im->render();    
    }

}

