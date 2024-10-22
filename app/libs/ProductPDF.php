<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

use Fpdf\Fpdf;

class ProductPDF extends Fpdf {
    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $size = 'letter'
    ) {
        parent::__construct( $orientation, $unit, $size );
        // ...
    }

    function getImageString($url) {
        $image_data = file_get_contents($url);
        $image_info = getimagesize($url);

        // Obtener el tipo de imagen desde la URL
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        dd($extension, 'EXT');

        return [
            'data' => $image_data,
            'type' => $extension
        ];
    }

    // Cabecera de página
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(0, 32, 96); // Color azul oscuro como en la imagen
        $this->Cell(0, 20, 'Pagina: ' . $this->PageNo() . ' Distribuidora Relmotor', 0, 1, 'L', true);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-50);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0);
        $this->MultiCell(0, 5, 
            "Amplia variedad de repuestos eléctricos como Alternadores, Motores de Partida, Despiece y otros.\n\n" .
            "Nuestras oficinas se encuentran en Santa Petronila 955, Quinta Normal, Santiago\n" .
            "contacto@tienda.relmotor.cl | www.relmotor.cl",
            0, 'C');
    }
}


