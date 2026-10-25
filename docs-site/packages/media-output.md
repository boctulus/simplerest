---

title: "Salida de imágenes y PDF"
source:
  - docs/packages/media-output.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Salida de imágenes y PDF

Los paquetes de esta guía crean contenido visual, pero usan APIs y dependencias diferentes. `simplerest-gd-image` trabaja con imágenes GD; `simplerest-fpdf` adapta FPDF para las convenciones de almacenamiento y respuesta de SimpleRest.

## Imágenes con `Imaginator`

`boctulus/simplerest-gd-image` aporta `Boctulus\Simplerest\Core\Libs\Imaginator` y requiere la extensión PHP `ext-gd`. La clase puede crear un lienzo, definir colores, dibujar texto, líneas, rectángulos y arcos, registrar formas, cargar otra imagen y combinar regiones. `render($image_format)` produce la salida de imagen; `loadImage($file)` carga una imagen desde una ruta.

La ruta, formato, dimensiones y permisos de escritura siguen siendo responsabilidad de la aplicación. El paquete no incluye assets web. No se probó lectura ni escritura de archivos.

## PDF con `PdfBase`

`boctulus/simplerest-fpdf` contiene `Boctulus\Simplerest\Core\Libs\PdfBase`, que envuelve `setasign/fpdf ^1.8`. El consumidor extiende la clase, usa las operaciones de FPDF y define el nombre, modo y ubicación de salida según su aplicación. El ejemplo del README guarda con `Output('F', ...)`; esa operación escribe en la ruta entregada.

```php
final class ReportPdf extends PdfBase
{
    public function render(): void
    {
        $this->AddPage();
        $this->Cell(0, 10, 'Monthly report');
    }
}
```

No se generó PDF ni se verificó una respuesta HTTP. Una salida a archivo requiere que el llamador seleccione y autorice la ruta.

## Dependencias

Ambos paquetes requieren el core; el paquete PDF también instala FPDF y el paquete de imagen requiere `ext-gd`. No hay contrato común de renderer ni instalación automática de fuentes o assets frontend.
