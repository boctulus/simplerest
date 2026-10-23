# SimpleRest PDF

Optional PDF integration for applications that use `boctulus/simplerest`.

Install it in the host application:

```sh
composer require boctulus/simplerest-pdf
```

The package provides `Boctulus\Simplerest\Core\Libs\PdfBase` and installs
`setasign/fpdf` as its PDF engine. The host application remains responsible for
its storage path and framework bootstrap.
