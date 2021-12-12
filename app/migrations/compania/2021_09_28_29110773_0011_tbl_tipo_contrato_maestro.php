<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoContratoMaestro60 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

 
        $table = ('tbl_tipo_contrato');
        $nom = 'tic';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNombre', 100)->comment('hashed')
        ->longtext($nom.'_lonDescripcion')->comment('hashed')
        ->varchar($nom.'_varCodigoDian',20)->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default("'0000-00-00'")
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')->default('1')
        ->integer('usu_intIdActualizador')->nullable();

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);
        $sc->foreign('usu_intIdCreador')->references('usu_intId')->on('tbl_usuario');
        $sc->foreign('usu_intIdActualizador')->references('usu_intId')->on('tbl_usuario');

        $res = $sc->create();

        DB::table($table)->insert(
            array(
                $nom.'_varNombre'=> 'CONTRATO POR OBRA LABOR'
                ,$nom.'_lonDescripcion'=>'Es un contrato que se realiza para una labor especifica y termina en el momento que la obra llegue a su fin. Este tipo de vinculacion es caracteritica de trabajos de construccion, de universidades y colegios. Este contrato es igual en terminos de beneficios y descuentos a los contratos indefinidos y definidos, por ser un contrato laboral.'
                ,$nom.'_varCodigoDian'=>'NA'
            )
        ); 

        DB::table($table)->insert(
            array(
                $nom.'_varNombre'=>'CONTRATO TERMINO FIJO'
                ,$nom.'_lonDescripcion'=>'Se caracteriza por tener una fecha de inicio y de terminacion que no puede superar 3 años, es fundamental que sea por escrito. Puede ser prorrogado indefinidamente cuando su vigencia sea superior a un (1) año, o cuando siendo inferior, se haya prorrogado hasta por tres (3) veces.'
                ,$nom.'_varCodigoDian'=>'NA'
            )
        ); 

        DB::table($table)->insert(
            array(
                $nom.'_varNombre'=>'CONTRATO TERMINO INDEFINIDO'
                ,$nom.'_lonDescripcion'=>'El contrato a termino indefinido no tiene estipulada una fecha de culmunicacion de la obligacion contractual, cuya duracion no haya sido expresamente estipulada o no resulte de la naturaleza de la obra o servicio que debe ejecutarse. Puede hacerse por escrito o de forma verbal.'
                ,$nom.'_varCodigoDian'=>'NA'
            )
        ); 

        DB::table($table)->insert(
            array(
                $nom.'_varNombre'=>'CONTRATO DE APRENDIZAJE'
                ,$nom.'_lonDescripcion'=>'Es aquel mediante el cual una persona natural realiza formacion practica en una entidad autorizada, a cambio de que la empresa proporcione los medios para adquirir fromacion profesional requerida en el oficio, actividad u ocupacion, por cualquier tiempo determinado no superior a dos (2) años, y por esto recibe un apoyo de sostenimiento mensual, que sea como mÃƒÂ­nimo en la fase lectiva el equivalente al 50% de un (1) salario minimo mensual vigente y durante la fase practica sera equivalente al setenta y cinco por ciento (75%) de un salario minimo mensual legal vigente. *No aplica para solicitudes de PEPFF y/o migrantes provenientes de Venezuela en condicion irregular.'
                ,$nom.'_varCodigoDian'=>'NA'
            )
        ); 

        DB::table($table)->insert(
            array(
                $nom.'_varNombre'=>'CONTRATO TEMPORAL, OCACIONAL O ACCIDENTAL'
                ,$nom.'_lonDescripcion'=>'Es aquel mediante el cual una persona natural realiza formacion  practica en una entidad autorizada, a cambio de que la empresa proporcione los medios para adquirir formacion profesional requerida en el oficio, actividad u ocupacion, por cualquier tiempo determinado no superior a dos (2) años, y por esto recibe un apoyo de sostenimiento mensual, que sea como minimo en la fase lectiva el equivalente al 50% de un (1) salario minimo mensual vigente y durante la fase practica sera¡ equivalente al setenta y cinco por ciento (75%) de un salario minimo mensual legal vigente. *No aplica para solicitudes de PEPFF y/o migrantes provenientes de Venezuela en condicion irregular.'
                ,$nom.'_varCodigoDian'=>'NA'
            )
        ); 
    }
}

