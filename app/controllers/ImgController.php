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

    function debug(){
        dd($_GET);

        Imaginator::disable();
        StdOut::showResponse();

        $this->render_01();
    }
    
    function test(){

        $pallets = $this->calc_pallets();
        $params  = http_build_query($_GET);
        
        ob_start();
        ?>

        <center style="margin-top: 20px;">
            <h3>This Layout Will Store <?= $pallets ?> Pallets 
                <a href="/img/debug?<?= $params ?>" target="_blank">
                    <img src="http://simplerest.lan/public/assets/img/debug-icon.jpg" width="50px" height="50px" style="margin-left:10px;">
                </a>
            </h3>  
        </center>

        <div class="container mt-4">
            <div class="row">
                <div class="col-md-3">
                <form action="/img/test" method="get">
                    <div class="mb-3 d-none">
                        <label for="design" class="form-label">Diseño</label>
                        <input type="text" class="form-control" id="design" name="design" value="multiple-rows">
                    </div>
                    <div class="mb-3 d-none">
                        <label for="condition" class="form-label">Condición</label>
                        <input type="text" class="form-control" id="condition" name="condition" value="new">
                    </div>
                    <div class="mb-3">
                        <label for="height" class="form-label">Altura</label>
                        <input type="text" class="form-control" id="height" name="height" value="<?= $_GET["height"] ?? 120 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="depth" class="form-label">Profundidad</label>
                        <input type="text" class="form-control" id="depth" name="depth" value="<?= $_GET["depth"] ?? 42 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="beam_length" class="form-label">Longitud de Viga</label>
                        <input type="text" class="form-control" id="beam_length" name="beam_length" value="<?= $_GET["beam_length"] ?? 96 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="beam_levels" class="form-label">Niveles de Viga</label>
                        <input type="text" class="form-control" id="beam_levels" name="beam_levels" value="<?= $_GET["beam_levels"] ?? 2 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="length" class="form-label">Longitud</label>
                        <input type="text" class="form-control" id="length" name="length" value="<?= $_GET["length"] ?? 25 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="width" class="form-label">Ancho</label>
                        <input type="text" class="form-control" id="width" name="width" value="<?= $_GET["width"] ?? 100 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="aisle" class="form-label">Pasillo</label>
                        <input type="text" class="form-control" id="aisle" name="aisle" value="<?= $_GET["aisle"] ?? 132 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="usesupport" class="form-label">Usar Soporte</label>
                        <input type="text" class="form-control" id="usesupport" name="usesupport" value="<?= $_GET["usesupport"] ?? 'false' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="usewiredeck" class="form-label">Usar Wiredeck</label>
                        <input type="text" class="form-control" id="usewiredeck" name="usewiredeck" value="<?= $_GET["usewiredeck"] ?? 'false' ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>

                </div>

                <div class="col-md-9"> 
                    <img src="/img/render_01?<?= $params ?>" id="rendered-img" width="100%" >
                </div>
            </div>
        </div>

        <?php

        $content = ob_get_clean();
        render($content);
    }

    function calc_pallets()
    {
        global $upright_height, $upright_depth, $beam_length, $beam_levels, $l_feets, $w_feets, $aisle, $len;
        global $w, $w_acc, $row_count, $boxes_per_row, $bl, $bl_with_margins;

        // Step 1
        $upright_height = (float) $_GET['height']; // inches

        $upright_depth  = (float) $_GET['depth'];   // inches     
        $beam_length    = (float) $_GET['beam_length'];   // inches * 

        $beam_levels    = (int)   $_GET['beam_levels'] ?? 2;

        // Step 3
        $l_feets        = (float) $_GET['length'];  // feet <-- length **
        $w_feets        = (float) $_GET['width'];;  // feet

        // Step 4
        $aisle          = (float) $_GET['aisle']; // inches
                
        /*
           Calculo
        */

        $len                  = M::toInches($l_feets);  // inches
        $w                    = M::toInches($w_feets);  // inches

        // StdOut::pprint($l - $upright_depth, "Max");

        // Calculo    

        $w_acc = $upright_depth;

        // StdOut::pprint($w_acc, 'w acc');

        // 42 + 60 + 2*42 + 60 + 2*42 + 60 + 2*42 + 60 + 42

        $row_count = 1;
        while ($row_count<999999 && $w_acc < $w - $upright_depth - $aisle) {
            $w_acc += $aisle + ($upright_depth * 2);
            $row_count += 1;

            // // StdOut::pprint("+= $aisle + ($upright_depth * 2)");
            // // StdOut::pprint($w_acc, 'w acc');
            // // StdOut::pprint($row_count, 'row count');
        }
    
        if ($w_acc < $w && $w_acc + $aisle + $upright_depth < $w){
            $w_acc += $aisle + $upright_depth;
            $row_count++;
        }

        StdOut::pprint(M::toFeetAndInches($w_acc), 'w acc');
        // StdOut::pprint("$row_count : row count");

        //  StdOut::pprint($h_feets, 'h');
        //  StdOut::pprint($aisle, 'aisle');
        //  StdOut::pprint$boxes_per_row, 'boxes per row'

        $boxes_per_row  = floor($len / $beam_length);

        $bl              = ($beam_length * $boxes_per_row);
        $bl_with_margins = (int) ($bl * 1.038);  // <------------- factor de correccion

        if ($bl_with_margins > $len){
            $boxes_per_row--;
        }

        $pallets = ($row_count -1) * $boxes_per_row * 12;

        switch ($beam_levels){
            case 3:
                $pallets = round( $pallets * 4/3);
                break;
            case 4:
                $pallets = round( $pallets * 5/3);
                break;
            case 5:
                $pallets = round( $pallets * 2);
                break;
            case 6:
                $pallets = round( $pallets * 7/3);
                break;
        }   

        return $pallets;
    }

    function render_01()
    {
        /*
            Seria mejor que fueran propiedades estaticas para evitar re-calcular
        */

        global $upright_height, $upright_depth, $beam_length, $beam_levels, $l_feets, $w_feets, $aisle, $len;
        global $w, $w_acc, $row_count, $boxes_per_row, $bl, $bl_with_margins;

        /*
            A veces puede quedar un poco "pasado" de ancho quedando el ultimo pasillo con alguna pulgada menos

            /img/test?design=multiple-rows&condition=new&height=96&depth=42&beam_length=96&beam_levels=2&length=50&width=200&aisle=132&usesupport=false&usewiredeck=false
        */

        $this->calc_pallets();

        $color_inv = true;

        // Definir dimensiones y colores
        $ancho = 600;
        $alto  = 600; // antes 1280

        $colors = [ 
            'white' => [255,255,255],
            'black' => [0,0,0],
            'steelblue' => [70,130,180]
        ];

        $font_1 = ASSETS_PATH . 'fonts/Swiss 721 Light BT.ttf';
        $font_2 = ASSETS_PATH . 'fonts/Swiss721BT-Light.otf';

        if ($row_count > 5){
            $alto *= intval($row_count/4.5); 
        }

        if ($boxes_per_row > 22){
            $ancho *= intval($boxes_per_row/22);
        }

        $margin_r  = max(intval($ancho * 0.1), 150);

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

        // Altura de todo el arreglo
        $y_dif = ($duos * $h) + ($interline * ($row_count-1)) - 1   - ($row_count -2) * $h -$h;

        // Ancho total especificado (convertido a pixels) por el usuario como parametro
        $y_usr = $y_dif * $w_feets / M::toFeet($w_acc);

        // Middle line
        $im->line($x_med, $y, 0, $y_dif, null, true);

        // Line at the right
        $im->line($x_end + 20, $y, 0, $y_usr);
   
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

        // Numero que aparece al centro y totaliza
        $im->text($x_med - 12, $y - 6, M::toFeetAndInches($bl_with_margins),   null, $font_2, 15);

        // Numero que aparece abajo de la primera celda
        $im->text($x + $w + 2 - 5 * strlen((string) $beam_length), $y + $w + 12, "$beam_length''",   null, $font_2, 15); 

        // Numero que aparece a la izquierda de la primera celda
        $im->text($x - 2, $y + $h  -3, "$upright_depth''"              , null, $font_2, 15);

        // Numero que aparece apaisado del lado derecho
        $im->text($x_end + 45, $y + floor($y_usr / 2), $w_feets . "'",   null, $font_2, 15, 90);

        // Leyendas de los pasillos (aisle)
        $lbl = M::toFeetAndInches($aisle);
        for ($i=0; $i<$row_count -1; $i++){
            $im->text($x_med - 20 - strlen($lbl) * 6, $y + $interline * ($i+0.5) +  0.5 * $h , $lbl, null, $font_2, 15);
        }
        
        // ...

        $im->render();                      
    }
    

     //////////////////////////////// x ///////////////////////////////////
   
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

