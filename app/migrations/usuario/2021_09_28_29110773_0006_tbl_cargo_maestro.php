<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCargoMaestro17 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
  
        $table = ('tbl_cargo');
        $nom = 'car';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varCodigo', 100)->comment('hashed')
        ->varchar($nom.'_varNombre', 100)->comment('hashed')
        ->longtext($nom.'_lonDescripcion')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp')
        ->integer('emn_intIdEmpresa')->nullable()
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')->default('1')
        ->integer('usu_intIdActualizador')->default('1');

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);
        $sc->foreign('emn_intIdEmpresa')->references('emn_intId')->on('tbl_empresa_nomina');

        $res = $sc->create();

        DB::table($table)->insert(
            array(
                $nom.'_varCodigo'=>'NA'
                ,$nom.'_varNombre'=>'NO APLICA'
                ,$nom.'_lonDescripcion'=> 'NO APLICA'
            )
        ); 

   }
}

