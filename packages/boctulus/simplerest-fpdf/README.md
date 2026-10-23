# SimpleRest FPDF

`boctulus/simplerest-fpdf` provides `PdfBase`, a small SimpleRest adapter around
[`setasign/fpdf`](https://github.com/Setasign/FPDF). It handles the framework's
PDF storage path, filename, and response presentation; FPDF remains the actual
PDF engine.

The package is not registered on Packagist yet. Install it from GitHub for
now:

```bash
composer config repositories.simplerest-fpdf vcs https://github.com/boctulus/simplerest-fpdf
composer require boctulus/simplerest-fpdf:dev-main
```
`setasign/fpdf` is installed as a declared Composer dependency, not copied into
this package.

The public class remains `Boctulus\Simplerest\Core\Libs\PdfBase` for existing
applications. The host application supplies its SimpleRest bootstrap and
storage configuration.

```php
final class ReportPdf extends PdfBase
{
    public function render(): void
    {
        $this->AddPage();
        $this->Cell(0, 10, 'Monthly report');
    }
}

$pdf = new ReportPdf();
$pdf->render();
$pdf->Output('F', ETC_PATH . '/pdfs/monthly-report.pdf');
```
