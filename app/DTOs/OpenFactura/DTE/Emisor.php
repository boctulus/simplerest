<?php

namespace simplerest\DTOs\OpenFactura\DTE;

class Emisor {
    public string $RUTEmisor;
    public string $RznSoc;
    public string $GiroEmis;
    public int $Acteco;
    public string $DirOrigen;
    public string $CmnaOrigen;
    public string $Telefono;
    public ?string $CdgSIISucur = null;
}