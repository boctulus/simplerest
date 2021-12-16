<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblGrupoCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_grupo_cuenta_contable`(
            `gru_intId` int(11) NOT NULL  auto_increment , 
            `gru_intCodigo` int(2) NOT NULL  , 
            `gru_varNombre` varchar(100) COLLATE utf8mb3_general_ci NOT NULL  , 
            `gru_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `gru_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `cla_intIdClase` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdActualizador` int(11) NOT NULL  DEFAULT 0 , 
            PRIMARY KEY (`gru_intId`,`gru_intCodigo`) , 
            UNIQUE KEY `gru_varNombre`(`gru_varNombre`) , 
            KEY `FK_gru_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_gru_idClase`(`cla_intIdClase`) , 
            KEY `FK_gru_idCreador`(`usu_intIdCreador`) , 
            CONSTRAINT `FK_gru_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_gru_idClase` 
            FOREIGN KEY (`cla_intIdClase`) REFERENCES `tbl_clase_cuenta_contable` (`cla_intId`) , 
            CONSTRAINT `FK_gru_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripción: \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Carlos Daniel Grajales Gil\r\n * Created: 10/07/2020\r\n * Update:\r\n * Fecha Update:\r\n *Modulo: BackOffice\r\n * Version Tabla: 1.0';
        
        ");

        Model::query("INSERT INTO `tbl_grupo_cuenta_contable` (`gru_intId`, `gru_intCodigo`, `gru_varNombre`, `gru_dtimFechaCreacion`, `gru_dtimFechaActualizacion`, `cla_intIdClase`, `usu_intIdCreador`, `usu_intIdActualizador`) VALUES
        (1, 11, 'Disponible', '2021-10-19 17:01:16', '1000-01-01 00:00:00', 1, 1, 1),
        (2, 12, 'Inversiones', '2021-10-19 17:02:15', '1000-01-01 00:00:00', 1, 1, 1),
        (3, 13, 'Deudores', '2021-10-19 17:05:19', '1000-01-01 00:00:00', 1, 1, 1),
        (4, 14, 'Inventarios', '2021-10-19 17:06:03', '1000-01-01 00:00:00', 1, 1, 1),
        (5, 15, 'Propiedades, Planta y Equipo', '2021-10-19 17:06:48', '1000-01-01 00:00:00', 1, 1, 1),
        (6, 16, 'Intangibles', '2021-10-19 17:07:35', '1000-01-01 00:00:00', 1, 1, 1),
        (7, 17, 'Diferidos', '2021-10-19 17:08:04', '1000-01-01 00:00:00', 1, 1, 1),
        (8, 18, 'Otros Activos', '2021-10-19 17:08:36', '1000-01-01 00:00:00', 1, 1, 1),
        (9, 19, 'Valorizaciones', '2021-10-19 17:09:21', '1000-01-01 00:00:00', 1, 1, 1),
        (10, 21, 'Obligaciones FInancieras', '2021-10-19 17:13:23', '1000-01-01 00:00:00', 2, 1, 1),
        (11, 22, 'Proveedores', '2021-10-19 17:14:30', '1000-01-01 00:00:00', 2, 1, 1),
        (12, 23, 'Cuentas por Pagar', '2021-10-19 17:15:02', '1000-01-01 00:00:00', 2, 1, 1),
        (13, 24, 'Impuestos, Gravamenes y Tasas', '2021-10-19 17:16:06', '1000-01-01 00:00:00', 2, 1, 1),
        (14, 25, 'Obligaciones Laborales', '2021-10-19 17:16:30', '1000-01-01 00:00:00', 2, 1, 1),
        (15, 26, 'Pasivos Estimados y Provisiones', '2021-10-19 17:16:30', '1000-01-01 00:00:00', 2, 1, 1),
        (20, 27, 'Diferidos Pasivos', '2021-10-19 17:19:44', '1000-01-01 00:00:00', 2, 1, 1),
        (21, 28, 'Otros Pasivos', '2021-10-19 17:20:56', '1000-01-01 00:00:00', 2, 1, 1),
        (22, 29, 'Bonos y Papeles Comerciales', '2021-10-19 17:20:56', '1000-01-01 00:00:00', 2, 1, 1),
        (23, 31, 'Capital Social', '2021-10-19 17:31:35', '1000-01-01 00:00:00', 3, 1, 1),
        (24, 32, 'Superavit de Capital', '2021-10-19 17:32:23', '1000-01-01 00:00:00', 3, 1, 1),
        (25, 33, 'Reservas', '2021-10-19 17:32:54', '1000-01-01 00:00:00', 3, 1, 1),
        (26, 34, 'Revalorizacion del Patriminio', '2021-10-19 17:33:26', '1000-01-01 00:00:00', 3, 1, 1),
        (27, 35, 'Dividendos o participaciones decretados en acciones, cuotas o partes de interés social', '2021-10-19 17:34:44', '1000-01-01 00:00:00', 3, 1, 1),
        (28, 36, 'Resultados del Ejercicio', '2021-10-19 17:35:13', '1000-01-01 00:00:00', 3, 1, 1),
        (29, 37, 'Resultados del Ejercicio Anteriores', '2021-10-19 17:36:31', '1000-01-01 00:00:00', 3, 1, 1),
        (30, 38, 'Superavit por Valoraciones', '2021-10-19 17:37:13', '1000-01-01 00:00:00', 3, 1, 1),
        (31, 41, 'Operacionales', '2021-10-19 17:40:51', '1000-01-01 00:00:00', 4, 1, 1),
        (32, 42, 'No operacionales', '2021-10-19 17:41:16', '1000-01-01 00:00:00', 4, 1, 1),
        (33, 47, 'Ajustes por Inflación', '2021-10-19 17:41:51', '1000-01-01 00:00:00', 4, 1, 1),
        (34, 51, 'Operacionales de Administracion', '2021-10-19 17:44:03', '1000-01-01 00:00:00', 5, 1, 1),
        (35, 52, 'Operaciones de Ventas', '2021-10-19 17:44:23', '1000-01-01 00:00:00', 5, 1, 1),
        (38, 53, 'No operacionales Gatos', '2021-10-19 17:45:18', '1000-01-01 00:00:00', 5, 1, 1),
        (39, 54, 'Impuestos de Renta y Complementarios', '2021-10-19 17:46:14', '1000-01-01 00:00:00', 5, 1, 1),
        (40, 59, 'Ganancias y Perdidas', '2021-10-19 17:46:41', '1000-01-01 00:00:00', 5, 1, 1),
        (41, 61, 'Costo de Ventas y de Prestacion de Servicios', '2021-10-19 17:48:24', '1000-01-01 00:00:00', 6, 1, 1),
        (42, 62, 'Compras', '2021-10-19 17:50:02', '1000-01-01 00:00:00', 6, 1, 1),
        (43, 71, 'Materia Prima', '2021-10-19 17:56:34', '1000-01-01 00:00:00', 7, 1, 1),
        (44, 72, 'Mano de Obra DIrecta', '2021-10-19 17:57:08', '1000-01-01 00:00:00', 7, 1, 1),
        (45, 73, 'Costos Indirectos', '2021-10-19 17:57:40', '1000-01-01 00:00:00', 7, 1, 1),
        (46, 74, 'Contratos de Servicios', '2021-10-19 21:47:46', '1000-01-01 00:00:00', 7, 1, 1),
        (47, 81, 'Derechos Contigentes', '2021-10-19 21:48:18', '1000-01-01 00:00:00', 8, 1, 1),
        (48, 82, 'Deudoras Fiscales', '2021-10-19 21:49:12', '1000-01-01 00:00:00', 8, 1, 1),
        (49, 83, 'Deudoras de Control', '2021-10-19 21:49:43', '1000-01-01 00:00:00', 8, 1, 1),
        (50, 84, 'Derechos Contingentes por contra (CR)', '2021-10-19 21:51:01', '1000-01-01 00:00:00', 8, 1, 1),
        (51, 85, 'Deudoras Fiscales por contra (CR)', '2021-10-19 21:51:43', '1000-01-01 00:00:00', 8, 1, 1),
        (52, 86, 'Deudoras de control por contra (CR)', '2021-10-19 21:52:51', '1000-01-01 00:00:00', 8, 1, 1),
        (53, 91, 'Responsabilidades Contingentes', '2021-10-19 21:55:05', '1000-01-01 00:00:00', 9, 1, 1),
        (54, 92, 'Acreedores Fiscales', '2021-10-19 21:59:20', '1000-01-01 00:00:00', 9, 1, 1),
        (55, 93, 'Acreedores de Control', '2021-10-19 21:59:57', '1000-01-01 00:00:00', 9, 1, 1),
        (56, 94, 'Responsabilidades Contingentes por Con', '2021-10-19 22:01:04', '1000-01-01 00:00:00', 9, 1, 1),
        (57, 95, 'Acreedores Fiscales por contra (DB)', '2021-10-19 22:03:06', '1000-01-01 00:00:00', 9, 1, 1),
        (58, 96, 'Acreedoras de Control por contra (DB)', '2021-10-19 22:05:02', '1000-01-01 00:00:00', 9, 1, 1);
    ");
    }
}

