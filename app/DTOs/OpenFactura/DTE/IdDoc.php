<?php

namespace simplerest\DTOs\OpenFactura\DTE;

class IdDoc {
    public int $TipoDTE;
    public int $Folio;    
    #[\DateTime]
    public string $FchEmis;
    public string $TpoTranVenta;
    public string $FmaPago;
}