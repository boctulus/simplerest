<?php

namespace Boctulus\OpenfacturaSdk\DTO;

class Receptor {
    public string $RUTRecep;
    public ?string $RznSocRecep = null;    
    public ?string $GiroRecep = null;
    public ?string $Contacto = null;    
    public ?string $DirRecep = null;
    public ?string $CmnaRecep = null;
}