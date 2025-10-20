<?php

namespace Boctulus\OpenfacturaSdk\DTO;

class IdDoc {
    public int $TipoDTE;
    public int $Folio;    
    #[\DateTime]
    public string $FchEmis;
    public string $TpoTranVenta;
    public string $FmaPago;
}