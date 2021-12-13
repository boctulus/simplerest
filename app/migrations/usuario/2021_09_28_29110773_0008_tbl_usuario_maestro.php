<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioMaestro24 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        $table = ('tbl_usuario');
        $nom = 'usu';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNroIdentificacion', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varNombre', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varNombre2', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varApellido', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varApellido2', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varNombreCompleto', 100)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varEmail', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varNumeroCelular', 20)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varExtension', 20)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varPassword', 20)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varToken', 50)->comment('hashed')->default("'NA'")
        ->varchar($nom.'_varTokenContrasena', 100)->comment('hashed')->default("'NA'")
        ->tinyint($nom.'_bolGetContrasena')->default('0')
        ->tinyint($nom.'_bolEstadoUsuario', 50)->comment('0')
        ->varchar($nom.'_varImagen', 250)->comment('hashed')->default("'NA'")
        ->integer($nom.'_intNumeroIntentos')->default('0')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaRecuperacion')->default('current_timestamp')
        ->integer('rol_intIdRol')->default('1')
        ->integer('car_intIdCargo')->default('1')
        ->integer('cdo_intIdTipoDocumento')->default('1')
        ->integer('est_intIdEstado')->default('1');

        
        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intIdEstado')->references($users_pri)->on($users_table);
        $sc->foreign('rol_intIdRol')->references('rol_intId')->on('tbl_rol');
        $sc->foreign('car_intIdCargo')->references('car_intId')->on('tbl_cargo');
        $sc->foreign('cdo_intIdTipoDocumento')->references('tid_intId')->on('tbl_tipo_documento');

        $res = $sc->create();

    }
}

