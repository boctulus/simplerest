<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblSubCuentaContableMaestro75 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        $table = ('tbl_sub_cuenta_contable');
        $nom = 'sub';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varCodigoCuenta', 30)->comment('hashed')
        ->varchar($nom.'_varNombreCuenta', 100)->comment('hashed')
        ->varchar($nom.'_varConceptoMedioMagnetico',50)->comment('hashed')
        ->varchar($nom.'_varEquivalenciaFisica',50)->comment('hashed')
        ->tinyint($nom.'_tinManejaTercero')->comment('hashed')
        ->tinyint($nom.'_tinManejaCentroCostos')->comment('hashed')
        ->tinyint($nom.'_tinManejaBase')->comment('hashed')
        ->integer($nom.'_intPorcentajeBase')->comment('hashed')
        ->decimal($nom.'_decMontobase' ,18.2)->comment('hashed')
        ->tinyint($nom.'_tinCuentaBalance')->comment('hashed')
        ->tinyint($nom.'_tinCuentaResultado')->comment('hashed')
        ->integer($nom.'_intPorcentajeBase')->comment('hashed')
        ->integer($nom.'_intPorcentajeBase')->comment('hashed')
        ->integer($nom.'_intPorcentajeBase')->comment('hashed')
        ->integer($nom.'_intPorcentajeBase')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default("'0000-00-00'")
        ->integer('mon_intIdMoneda')
        ->integer('ccc_intIdCategoriaCuentaContable')
        ->integer('cue_intIdCuentaContable')
        ->integer('nat_intIdNaturalezaCuentaContable')
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable();

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);
        $sc->foreign('usu_intIdCreador')->references('usu_intId')->on('tbl_usuario');
        $sc->foreign('usu_intIdActualizador')->references('usu_intId')->on('tbl_usuario');

        $res = $sc->create(true);


    }
}

