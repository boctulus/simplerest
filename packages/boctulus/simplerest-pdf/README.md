# SimpleRest PDF

Optional PDF integration for applications that use `boctulus/simplerest`.

This package is not registered on Packagist yet. A Packagist-only Composer
require command cannot resolve it until its split repository is registered.
Before that, consumers must declare the package's Composer VCS repository
explicitly and require a version present in that repository.

The package provides `Boctulus\Simplerest\Core\Libs\PdfBase`. Composer resolves
`setasign/fpdf` as its PDF engine. The host application remains responsible for
its storage path and framework bootstrap.
