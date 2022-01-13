<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCuentaContableMaestro87 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
    
        $table = ('tbl_cuenta_contable');
        $nom = 'cue';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNumeroCuenta', 4)->comment('hashed')->unique()
        ->varchar($nom.'_varNombreCuenta', 50)->comment('hashed')
        ->tinyint($nom.'_tinCuentaBalance')->comment('hashed')
        ->tinyint($nom.'_tinCuentaResultado')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default("'0000-00-00'")
        ->integer('gru_intIdGrupoCategoriaCuentaContable')->index()
        ->integer('ccc_intIdCategoriaCuentaContable')->index()
        ->integer('est_intIdEstado')->default('1')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable();

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

       //$sc->foreign('gru_intIdGrupoCategoriaCuentaContable')->references('gru_intId')->on('tbl_grupo_cuenta_contable');
        //$sc->foreign('ccc_intIdCategoriaCuentaContable')->references('ccc_intId')->on('tbl_categoria_cuenta_contable');
        $sc->foreign('est_intIdEstado')->references($users_pri)->on($users_table);
        $sc->foreign('usu_intIdCreador')->references('usu_intId')->on('tbl_usuario');
        $sc->foreign('usu_intIdActualizador')->references('usu_intId')->on('tbl_usuario');

        $res = $sc->create();
    }
}

