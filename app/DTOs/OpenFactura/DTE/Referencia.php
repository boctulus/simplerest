<?php

namespace Boctulus\Simplerest\DTOs\OpenFactura\DTE;

class Referencia {
    public int $NroLinRef;
    public string $TpoDocRef;
    public string $FolioRef;
    #[\DateTime]
    public string $FchRef;
    public string $CodRef;
}