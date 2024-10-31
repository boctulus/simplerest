<?php

namespace simplerest\libs;

use simplerest\core\libs\PdfBase;

/*
    Ej de uso:

    $pdf = new ProductPDF();
    $pdf->setData($product);
    $pdf->render();            

    $pdf->Output();

    O... para descargarlo:

    // Crear nuevo PDF
    $pdf = new ProductPDF();
    $pdf->setData($product['data']);
    $pdf->render();            

    // $pdf->Output();

    $filename = $product['data']['name'] . '.pdf';
    $pdf->prepareDownload($filename);
*/
class ProductPDF extends PdfBase 
{
    protected $margin = 20;

    public function __construct(
        $orientation = 'P',
        $unit = '',
        $size = []
    ) {
        parent::__construct($orientation, $unit, $size);

        // Añadir la fuente personalizada Roboto
        // $this->AddFont('Roboto', '', 'Roboto-Regular.ttf');
        // $this->AddFont('Roboto', 'B', 'Roboto-Bold.ttf');
    }

    protected function fixDescription($description, $max_word_count = 50, $max_char_len = 200, bool $add_dots = true){
        $description = utf8_decode($description);        
        $description = str_replace('&nbsp;', '', $description);
        $description = strip_shortcodes($description); // remuevo [audio mp3=""][/audio], etxc
        $description = Strings::removeMultipleSpaces($description);
        $description = Strings::getUpTo($description, $max_word_count, $max_char_len, $add_dots);

        return $description;
    }

    // Cabecera de página
    function Header() {
        $this->SetY(0); // Posicionar el header en la parte superior de la página
        $this->SetX(0); // Posicionar el contenido al borde izquierdo
        $this->SetRightMargin(0); 

        $this->SetFont('Arial', 'B', 10); // Reducido 
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(5,41,94); // Color azul oscuro
        
        // Reducida la altura de 20 a 15 y agregado padding left de 10
        $this->Cell(0, 15, '   Pagina: ' . $this->PageNo() . ' Distribuidora Relmotor', 0, 1, 'L', true);

        $this->SetRightMargin($this->margin);
        $this->SetAutoPageBreak(true, 25);
    }
    
    function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0);
        
        // Primera línea
        $this->MultiCell(0, 5, 
            utf8_decode("Amplia variedad de repuestos eléctricos como Alternadores, Motores de Partida, Despiece y otros."),
            0, 'C');
        $this->Ln(2);
        
        // Dirección con enlace
        $texto_direccion = "Nuestras oficinas se encuentran en Santa Petronila 955, Quinta Normal, Santiago";
        $texto_inicio = "Nuestras oficinas se encuentran en ";
        $texto_enlace = "Santa Petronila 955, Quinta Normal, Santiago";
        
        // Calcular posición X para centrar
        $ancho_total = $this->GetStringWidth(utf8_decode($texto_direccion));
        $x_inicio = ($this->GetPageWidth() - $ancho_total) / 2;
        
        $this->SetX($x_inicio);
        $this->Write(5, utf8_decode($texto_inicio));
        
        // Agregar el enlace
        $this->SetTextColor(6,36,59); // Color azul para el enlace
        $x_enlace = $this->GetX();
        $y_enlace = $this->GetY();
        $ancho_enlace = $this->GetStringWidth(utf8_decode($texto_enlace));
        $this->Write(5, utf8_decode($texto_enlace), 'https://g.page/relmotor');
        
        // Restaurar color
        $this->SetTextColor(5, 41, 94);
        $this->Ln(5);

        $this->SetTextColor(165,74,90); /// Rojo enlaces aqui       
        $this->MultiCell(0, 5,
            utf8_decode("contacto@tienda.relmotor.cl | www.relmotor.cl"),
            0, 'C');
    }

    function render(){
        $this->AddPage();

        // Convertir strings con caracteres especiales, ..., acortar
        $description = $this->fixDescription($this->data['description'], 50, 200);

        // Datos del producto
        
        // Agregar imagen
        $extension = strtoupper(pathinfo($this->data['featured_image'], PATHINFO_EXTENSION));
        $this->Image($this->data['featured_image'], 10, 40, 90, $extension);

        // SKU
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(5,41,94); // Azul oscuro para SKU
        $this->SetXY($this->margin, 140);
        $this->Cell(0, 10, 'SKU: ' . $this->data['sku']);

        // Descripción
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0);
        $this->SetXY($this->margin, 150);
        $this->MultiCell(0, 5, $description);

        // Marca
        $this->SetXY($this->margin, 172);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'MARCA');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, $this->data['attributes'][0]['value']);

        // // Precio
        // $this->SetXY($this->margin, 180);
        // $this->SetFont('Arial', 'B', 14);
        // $this->SetTextColor(255, 0, 0); // Rojo para el precio
        // $this->Cell(0, 10, 'Precio: ' . Money::getFormatted($this->data['price']) . ' Neto');

        // Stock de se_resultset[0].stock_status

        $instock = ($this->data['stock_status'] == 'instock');

        $this->SetFont('Arial', 'B', 12);

        if ($instock){
            $this->SetTextColor(25,135,84); 
        } else {
            $this->SetTextColor(220,53,69);
        }

        $this->SetXY($this->margin, 180);
        $this->Cell(0, 10, $instock ? 'EN STOCK' : 'Sin stock');
    }
}

