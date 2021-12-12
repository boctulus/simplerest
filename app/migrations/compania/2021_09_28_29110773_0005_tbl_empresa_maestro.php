<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpresaMaestro12 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        $table = ('tbl_empresa');
        $nom = 'emp';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varRazonSocial', 500)->comment('hashed')
        ->varchar($nom.'_varNit', 15)->comment('hashed')
        ->varchar($nom.'_varEmail',100)->comment('hashed')
        ->varchar($nom.'_varCelular',50)->comment('hashed')
        ->varchar($nom.'_varTipoCuenta',20)->comment('hashed')
        ->varchar($nom.'_varNumeroCuenta',50)->comment('hashed')
        ->varchar($nom.'_varPila',350)->comment('hashed')
        ->integer($nom.'_intAnoConstitucion')->default('0')
        ->tinyint($nom.'_bolAplicarLey14292020')->default('0')
        ->tinyint($nom.'_bolAplicarLey5902000')->default('0')
        ->tinyint($nom.'_bolAportaParafiscales16072012')->default('0')
        ->tinyint($nom.'_bolAplicaDecreto5582000')->default('0')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp')
        ->integer('arl_intIdArl')->default('1')
        ->integer('opp_intIdOperador')->default('1')
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')->default('1')
        ->integer('usu_intIdActualizador')->default('1');

         $users_table = 'tbl_estado';
         $users_pri   = 'est_intId';

         $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);
         $sc->foreign('arl_intIdArl')->references('arl_intId')->on('tbl_arl');
         $sc->foreign('opp_intIdOperador')->references('opp_intId')->on('tbl_operador_pila');

         $res = $sc->create(true);

         DB::table($table)->insert(
            array(
                $nom.'_varRazonSocial'=>'NA'
                ,$nom.'_varNit'=>'1'
                ,$nom.'_varEmail'=> 'NA@HOTMAIL.COM'
                ,$nom.'_varCelular'=> '1'
                ,$nom.'_varTipoCuenta'=> '1'
                ,$nom.'_varNumeroCuenta'=> '1'
                ,$nom.'_varPila'=> '1'
                ,$nom.'_intAnoConstitucion'=> '1'
                ,$nom.'_bolAplicarLey14292020'=> '0'
                ,$nom.'_bolAplicarLey5902000'=> '0'
                ,$nom.'_bolAportaParafiscales16072012'=> '0'
                ,$nom.'_bolAplicaDecreto5582000'=> '0'
                ,'arl_intIdArl'=> '1'
                ,'opp_intIdOperador'=> '1'
                ,'usu_intIdCreador'=> '1'
                ,'usu_intIdActualizador'=> '1'
            )
        ); 
    }
}

