<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblConceptoNomina implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        /*
            Se corrige
            
            Syntax error or access violation: 1101 BLOB, TEXT, GEOMETRY or JSON column 'cpn_lonDescripcion' can't have a default value
        */
        Model::query("CREATE TABLE `tbl_concepto_nomina`(
            `cpn_intId` int(11) NOT NULL  auto_increment , 
            `cpn_varCodigo` varchar(100) COLLATE armscii8_general_ci  NULL  DEFAULT '' , 
            `cpn_varNombre` varchar(100) COLLATE armscii8_general_ci NOT NULL  DEFAULT '' , 
            `cpn_lonDescripcion` LONGTEXT COLLATE armscii8_general_ci  NULL, 
            `cpn_varFormula` varchar(100) COLLATE armscii8_general_ci NOT NULL  DEFAULT '' , 
            `cpn_varTipoDeConcepto` varchar(20) COLLATE armscii8_general_ci NOT NULL  DEFAULT '' , 
            `cpn_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `cpn_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdActualizador` int(11) NULL  DEFAULT 1 , 
            PRIMARY KEY (`cpn_intId`) , 
            KEY `fk_id_Estado`(`est_intIdEstado`) , 
            KEY `fk_id_Creador`(`usu_intIdCreador`) , 
            KEY `fk_id_Actualizador`(`usu_intIdActualizador`) , 
            CONSTRAINT `fk_id_Actualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `fk_id_Creador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `fk_id_Estado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION 
        ) ENGINE=InnoDB DEFAULT CHARSET='armscii8' COLLATE='armscii8_general_ci';
        ");

        Model::query("INSERT INTO `tbl_concepto_nomina` (`cpn_intId`, `cpn_varCodigo`,cpn_varNombre, `cpn_lonDescripcion`, `cpn_varFormula`, `cpn_varTipoDeConcepto`, `est_intIdEstado`, `usu_intIdCreador`, `usu_intIdActualizador`) VALUES
        (1, '0001',' ', 'CESANTIAS ', '(SALARIO MENSUAL * DIAS TRABAJO)/360', '', 1, 1, 1),
        (2, '0002',' ', 'INTERESES A LAS CESANTIAS', '(CESANTIAS * DIAS TRABAJADOS * 0.12)/360', '', 1, 1, 1),
        (3, '0003',' ', 'PRIMA DE SERVICIOS ', '(SALARIO MES*DIAS TRABAJADOS SEMESTRE)/360', '', 1, 1, 1),
        (4, '0004',' ', 'VACACIONES ', '(SALARIO*DIAS TRABAJADOS)/720', '', 1, 1, 1),
        (5, '0005',' ', 'TRABAJO NORTURNO ENTRE 10 PM Y 6 AM', '(HORA ORDINARIA * 0.35)', '', 1, 1, 1),
        (6, '0006',' ', 'HORA EXTRA DIURANA ENTRE 6 AM Y 10 P.M', '(HORA ORDINARIA * 1.25)', '', 1, 1, 1),
        (7, '0007',' ', 'HORA EXTRA NOCTURNA ENTRE LAS 10 PM Y LAS 6 AM ', '(HORA ORDINARIA * 1.75)', '', 1, 1, 1),
        (8, '0008',' ', 'HORA ORDINARIA DOMINICAL O FESTIVA ', '(HORA ORDINARIA * 1.75)', '', 1, 1, 1),
        (9, '0009',' ', 'HORA EXTRA NOCTURNA DOMINICAL O FESTIVA ', '(HORA ORDINARIA * 2)', '', 1, 1, 1),
        (10, '0010',' ', 'INDEMNIZACION CONTRATO A TERMINO FIJO VALOR DE LOS SALARIOS QUE FALTEN POR TERMINAR EL CONTRATO', 'FALTA FORMULA', '', 1, 1, 1),
        (12, '0011',' ', 'INDEMNIZACION CONTRATO A TERMINOD INDEFINIDO SALARIOS INFERIORES A 10 MINIMOS: 30 DIS POR EL PRIMER ANO Y 20 POR CADA ANO SIGUIENTE O PROPORCION MAS DE 10 SARIOS MINIMOS 20 DIAS POR EL PRIMER ANO Y 15 POR CADA UNO DE  LOS SIGUIENTES ANOS O PROPORCINAL', 'FALTA FORMULA', '', 1, 1, 1);
    ");
    }
}

