<?php

namespace Boctulus\OpenfacturaSdk\DTO;

class Referencia {
    public int $NroLinRef;
    public string $TpoDocRef;
    public string $FolioRef;
    #[\DateTime]
    public string $FchRef;
    public string $CodRef;
}