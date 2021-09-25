<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoContrato implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_tipo_contrato 
(
  tic_varNombre,tic_varDescripcion,tic_varCodigoDian, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
  'CONTRATO POR OBRA LABOR', 'Es un contrato que se realiza para una labor especÃƒÂ­fica y termina en el momento que la obra llegue a su fin. Este tipo de vinculaciÃƒÂ³n es caracterÃƒÂ­stica de trabajos de construcciÃƒÂ³n, de universidades y colegios. Este contrato es igual en tÃƒÂ©rminos de beneficios y descuentos a los contratos indefinidos y definidos, por ser un contrato laboral.','NA', 1, 1
),
(
  'CONTRATO TERMINO FIJO', 'Se caracteriza por tener una fecha de inicio y de terminaciÃƒÂ³n que no puede superar 3 aÃƒÂ±os, es fundamental que sea por escrito. Puede ser prorrogado indefinidamente cuando su vigencia sea superior a un (1) aÃƒÂ±o, o cuando siendo inferior, se haya prorrogado hasta por tres (3) veces.','NA', 1, 1
),
(
  'CONTRATO TERMINO INDEFINIDO', 'El contrato a tÃƒÂ©rmino indefinido no tiene estipulada una fecha de culminaciÃƒÂ³n de la obligaciÃƒÂ³n contractual, cuya duraciÃƒÂ³n no haya sido expresamente estipulada o no resulte de la naturaleza de la obra o servicio que debe ejecutarse. Puede hacerse por escrito o de forma verbal.','NA', 1, 1
),
(
  'CONTRATO DE APRENDIZAJE', 'Es aquel mediante el cual una persona natural realiza formaciÃƒÂ³n teÃƒÂ³rica prÃƒÂ¡ctica en una entidad autorizada, a cambio de que la empresa proporcione los medios para adquirir formaciÃƒÂ³n profesional requerida en el oficio, actividad u ocupaciÃƒÂ³n, por cualquier tiempo determinado no superior a dos (2) aÃƒÂ±os, y por esto recibe un apoyo de sostenimiento mensual, que sea como mÃƒÂ­nimo en la fase lectiva el equivalente al 50% de un (1) salario mÃƒÂ­nimo mensual vigente y durante la fase prÃƒÂ¡ctica serÃƒÂ¡ equivalente al setenta y cinco por ciento (75%) de un salario mÃƒÂ­nimo mensual legal vigente. *No aplica para solicitudes de PEPFF y/o migrantes provenientes de Venezuela en condiciÃƒÂ³n irregular.','NA', 1, 1
),
(
  'CONTRATO TEMPORAL, OCACIONAL O ACCIDENTAL', 'Es aquel mediante el cual una persona natural realiza formaciÃƒÂ³n teÃƒÂ³rica prÃƒÂ¡ctica en una entidad autorizada, a cambio de que la empresa proporcione los medios para adquirir formaciÃƒÂ³n profesional requerida en el oficio, actividad u ocupaciÃƒÂ³n, por cualquier tiempo determinado no superior a dos (2) aÃƒÂ±os, y por esto recibe un apoyo de sostenimiento mensual, que sea como mÃƒÂ­nimo en la fase lectiva el equivalente al 50% de un (1) salario mÃƒÂ­nimo mensual vigente y durante la fase prÃƒÂ¡ctica serÃƒÂ¡ equivalente al setenta y cinco por ciento (75%) de un salario mÃƒÂ­nimo mensual legal vigente. *No aplica para solicitudes de PEPFF y/o migrantes provenientes de Venezuela en condiciÃƒÂ³n irregular.','NA', 1, 1
)
;");
    }
}

