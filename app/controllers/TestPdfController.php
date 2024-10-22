<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\libs\ProductPDF;
use simplerest\core\libs\Strings;
use simplerest\core\controllers\Controller;
use simplerest\core\traits\TimeExecutionTrait;

class TestPdfController extends Controller
{
    // use TimeExecutionTrait;

    function index()
    {
        /*
            El precio va ser necesario formatearlo
        */

        $json = '
        {
            "id": "25432",
            "description": "(1.205/4, 1.135/4) 12V - CARBON 4.5X14 - USA HISX8128 - OPEL COMBO DIESEL, CORSA, NISSAN SENTRA, ISUZU, FIAT - RCP 18575, UNIFAP 12054, 11354, SCHUNK PE1046, VF HT207, WAI 698118",
            "sku": "SBH2002",
            "price": "7723",
            "featured_image": "http://relmotor.lan/wp-content/uploads/2024/07/f7ab9SBH2002-350x350.jpg",
            "attributes": [
                {
                    "name": "Marca",
                    "value": "AS PARTS"
                },
                {
                    "name": "Sistema Electrico",
                    "value": "BOSCH"
                }
            ]
        }
        ';

        try {
            $product = json_decode($json, true);


            // Crear nuevo PDF
            $pdf = new ProductPDF();
            $pdf->setData($product);
            $pdf->render();            

            // $pdf->Output();

            $filename = 'pdfs/temp.pdf';
            $filepath = ETC_PATH . $filename;

            // ... todo tu código de generación del PDF ...
            $pdf->Output('F', $filepath); // 'F' significa guardar a archivo

            $this->show();

            // Limpiamos el archivo temporal
            unlink($filepath);
            exit;

        } catch (\Exception $e) {
            // Llama al método del trait
            dd($e->getMessage());
        }
    }

    function show(){
        $filename = 'pdfs/temp.pdf';
        $filepath = ETC_PATH . $filename;

        // Ahora enviamos el archivo al navegador
        if (!file_exists($filepath)) {
            throw new \Exception("El PDF no se pudo generar o guardar");
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Cache-Control: public, max-age=0');
        header('Content-Length: ' . filesize($filepath));
        header('Content-Transfer-Encoding: binary');
        
        // Limpiamos cualquier salida previa
        ob_clean();
        flush();
        
        readfile($filepath);
    }
}


