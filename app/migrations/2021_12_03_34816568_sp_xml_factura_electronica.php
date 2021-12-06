<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class SpXmlFacturaElectronica implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

    /*-- --------------------------------------------------------
    -- Host:                         190.29.102.172
    -- Versión del servidor:         10.6.4-MariaDB-1:10.6.4+maria~focal - mariadb.org binary distribution
    -- SO del servidor:              debian-linux-gnu
    -- HeidiSQL Versión:             11.3.0.6295
    -- Desarrrollador : Jose Perez
    -- Nombre : sp_xml_factura_electronica
    -- Fecha : 03/12/2021
    -- --------------------------------------------------------*/

      DB::statement("DROP PROCEDURE IF EXISTS sp_xml_factura_electronica; ");

      DB::statement(file_get_contents(ETC_PATH.'sp_xml_factura_electronica.sql'));
    }

    public function down(){

      DB::statement("DROP PROCEDURE IF EXISTS sp_xml_factura_electronica; ");

    }


}

