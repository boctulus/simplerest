<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRolMaestro3 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        $table = ('tbl_rol');
        $nom = 'rol';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNombre', 100)->comment('hashed')
        ->longtext($nom.'_varDescripcion')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp')
        ->integer('est_intEstado')->default('1');

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);

        $res = $sc->create();

        DB::table($table)->insert(
            array('rol_varNombre'=>'NO APLICA', 'rol_varDescripcion'=> 'NO APLICA')
        ); 

          
    }
}

