<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCategoriaDocumentoMaestro21 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        $table = ('tbl_tipo_documento');
        $nom = 'tid';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varTipoDocumento', 50)->comment('hashed')
        ->varchar($nom.'_varSiglas', 3)->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp')
        ->integer('est_intIdEstado')->default('1')
        ->integer('usu_intIdCreador')->default('1')
        ->integer('usu_intIdActualizador')->default('1');

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intIdEstado')->references($users_pri)->on($users_table);

        $res = $sc->create();

        DB::table($table)->insert(
            array(
                $nom.'_varTipoDocumento'=>'Cedula Ciudadania'
                ,$nom.'_varSiglas'=>'CC'
            )
        ); 
                                        
        
        DB::table($table)->insert(
            array(
                $nom.'_varTipoDocumento'=>'Registro Civil'
                ,$nom.'_varSiglas'=>'R.C'
            )
        ); 


    }
}

