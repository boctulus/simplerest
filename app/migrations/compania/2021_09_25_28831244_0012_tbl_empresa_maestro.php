<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpresa implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_empresa (
  emp_intId int(11) NOT NULL AUTO_INCREMENT,
  emp_varRazonSocial varchar(300) NOT NULL,
  emp_varNit varchar(20) NOT NULL,
  emp_varEmail varchar(100) NOT NULL,
  emp_varCelular varchar(50) NOT NULL,
  emp_varTipoCuenta varchar(20) NOT NULL,
  emp_varNumeroCuenta varchar(50) NOT NULL,
  emp_varPila varchar(350) NOT NULL,
  emp_intAnoConstitucion int(11) NOT NULL DEFAULT 0,
  emp_bolAplicarLey14292020 tinyint(4) NOT NULL DEFAULT 0,
  emp_bolAplicarLey5902000 tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  emp_bolAportaParafiscales16072012 tinyint(4) NOT NULL DEFAULT 0,
  emp_bolAplicaDecreto5582000 tinyint(4) NOT NULL DEFAULT 0,
  emp_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  emp_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  arl_intIdArl int(11) DEFAULT NULL,
  opp_intIdOperador int(11) DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (emp_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_empresa 
 * Author: http://www.divergente.net.co");
    }
}

