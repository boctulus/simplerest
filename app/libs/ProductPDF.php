<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

use Fpdf\Fpdf;

/*
    Ej de uso:

    $pdf = new ProductPDF();
    $pdf->setData($product);
    $pdf->render();            

    $pdf->Output();
*/
class ProductPDF extends Fpdf {
    protected $data;

    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $size = 'letter'
    ) {
        parent::__construct( $orientation, $unit, $size );
        // ...
    }

    function setData(array $product){
        $this->data = $product;
        return $this;
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

    function render(){
        $this->AddPage();

        // Datos del producto
        
        // Agregar imagen
        $extension = strtoupper(pathinfo($this->data['featured_image'], PATHINFO_EXTENSION));
        $this->Image($this->data['featured_image'], 10, 40, 90, $extension);

        // Información del producto
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 32, 96); // Azul oscuro para SKU
        $this->SetXY(10, 140);
        $this->Cell(0, 10, 'SKU: ' . $this->data['sku']);

        // Descripción
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0);
        $this->SetXY(10, 150);
        $this->MultiCell(0, 5, $this->data['description']);

        // Marca
        $this->SetXY(10, 170);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'Marca');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, $this->data['attributes'][0]['value']);

        // Precio
        $this->SetXY(10, 180);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(255, 0, 0); // Rojo para el precio
        $this->Cell(0, 10, 'Precio: $' . number_format($this->data['price'], 0, ',', '.') . ' Neto');
    }
}


