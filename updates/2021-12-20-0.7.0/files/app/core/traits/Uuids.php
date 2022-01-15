<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:56              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\traits; trait Uuids { protected function boot() { parent::boot(); $this->registerInputMutator($this->getIdName(), function ($GabDK) { return uuid_create(UUID_TYPE_RANDOM); }, function ($MrHHt, $vdcnq) { return $MrHHt == "\103\122\105\x41\124\105"; }); } }
