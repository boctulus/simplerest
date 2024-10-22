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

            // Crear nuevo PDF
            $pdf = new ProductPDF();
            $pdf->AddPage();

            // Datos del producto
            $product = json_decode($json, true);

            // Agregar imagen
            $extension = strtoupper(pathinfo($product['featured_image'], PATHINFO_EXTENSION));
            $pdf->Image($product['featured_image'], 10, 40, 90, $extension);

            // Información del producto
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(0, 32, 96); // Azul oscuro para SKU
            $pdf->SetXY(10, 140);
            $pdf->Cell(0, 10, 'SKU: ' . $product['sku']);

            // Descripción
            $pdf->SetFont('Arial', '', 11);
            $pdf->SetTextColor(0);
            $pdf->SetXY(10, 150);
            $pdf->MultiCell(0, 5, $product['description']);

            // Marca
            $pdf->SetXY(10, 170);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 10, 'Marca');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 10, $product['attributes'][0]['value']);

            // Precio
            $pdf->SetXY(10, 180);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(255, 0, 0); // Rojo para el precio
            $pdf->Cell(0, 10, 'Precio: $' . number_format($product['price'], 0, ',', '.') . ' Neto');

            $pdf->Output();

        } catch (\Exception $e) {
            // Llama al método del trait
            dd($e->getMessage());
        }
    }

}


