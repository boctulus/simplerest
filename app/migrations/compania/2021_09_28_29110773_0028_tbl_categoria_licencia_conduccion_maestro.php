<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaLicenciaConduccionMaestro141 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_licencia_conduccion (
        clc_intId int(11) NOT NULL AUTO_INCREMENT,
        clc_varNombre varchar(50) NOT NULL,
        clc_varDescripcion varchar(250) NOT NULL,
        clc_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        clc_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (clc_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_categoria_licencia_conduccion 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_categoria_licencia_conduccion ADD CONSTRAINT FK_clc_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_categoria_licencia_conduccion ADD CONSTRAINT FK_clc_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_categoria_licencia_conduccion ADD CONSTRAINT FK_clc_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

        Model::query("INSERT INTO tbl_categoria_licencia_conduccion (clc_varNombre,clc_varDescripcion,usu_intIdCreador, usu_intIdActualizador)
        VALUES ('A2','Para motocicletas, motociclos y mototriciclos de mÃƒÂ¡s de 125 CC de cilindrada.',1,1),
        ('B1','Para autÃƒÂ³moviles, motocarros, cuatrimotos, camperos, camionetas y microbuses de servicio particular.',1,1),
        ('C1','Para automÃƒÂ³viles, camperos, camionetas y microbuses de servicio pÃƒÂºblico.',1,1),
        ('C2','Para conducir camiones rÃƒÂ­gidos, buses ybusetas de servicio particular. Antigua CategorÃƒÂ­a 5.',1,1);");
    }
}

