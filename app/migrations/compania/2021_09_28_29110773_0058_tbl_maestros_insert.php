<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaPersonaInsert353 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    { 
        Model::query("INSERT INTO tbl_categoria_persona(cap_intId, cap_varCategoriaPersona, cap_dtimFechaCreacion, cap_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
        (1, 'Empleado', '2021-05-20 11:40:29', '2021-06-30 09:38:58', 1, 1, 1),
        (2, 'Tercero', '2021-05-20 11:40:58', '2021-07-21 21:44:46', 1, 1, 1),
        (3, 'Visitante', '2021-06-25 15:21:04', '1000-01-01 00:00:00', 1, 1, 1),
        (4, 'Cliente', '2021-08-04 16:48:49', '1000-01-01 00:00:00', 1, 1, 1),
        (5, 'Proveedor', '2021-08-04 16:48:58', '1000-01-01 00:00:00', 1, 1, 1),
        (6, 'Interesado', '2021-08-04 16:49:09', '1000-01-01 00:00:00', 1, 1, 1);");

        Model::query("INSERT INTO tbl_descuento(des_intId, des_varDescuento, des_decDescuento, des_dtimFechaCreacion, des_timFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
        (1, 'Descuento uno', 10.00, '2021-09-06 10:54:36', '1000-01-01 00:00:00', 1, 1, 1),
        (2, 'Descuento dos', 20.00, '2021-09-06 10:54:36', '1000-01-01 00:00:00', 1, 1, 1),
        (3, 'Descuento Tres', 30.00, '2021-09-06 10:54:36', '1000-01-01 00:00:00', 1, 1, 1);
        ");

        Model::query("INSERT INTO tbl_dias_pago(dpa_intId, dpa_intDiasPago, dpa_dtimFechaCreacion, dpa_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
        (1, 30, '2021-09-06 10:53:05', '1000-01-01 00:00:00', 1, 1, 1),
        (2, 60, '2021-09-06 10:53:05', '1000-01-01 00:00:00', 1, 1, 1),
        (3, 90, '2021-09-06 10:53:05', '1000-01-01 00:00:00', 1, 1, 1);");

        Model::query("INSERT INTO tbl_moneda(mon_intId, mon_varCodigoMoneda, mon_varNombre,mon_lonDescripcion, mon_dtimFechaCreacion, mon_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
        (1, '00001', 'Pesos','Pesos', '2021-09-03 20:33:37', '1000-01-01 00:00:00', 1, 1, 1);");

        Model::query("INSERT INTO tbl_pais(pai_varCodigo, pai_varPais, pai_varCodigoPaisCelular,pai_intIdMoneda, usu_intIdCreador, usu_intIdActualizador) VALUES
        ('169', 'Colombia', '+57',1,  1, 1),
        ('196', 'Costa Rica', '+50',1, 1, 1);");

        
        Model::query("INSERT INTO tbl_transaccion( tra_varTransaccion, usu_intIdCreador, usu_intIdActualizador) VALUES
        ('Factura', 1, 1),
        ('Cotizacion', 1, 1),
        ('Compras', 1, 1),
        ('Orden Compra', 1, 1),
        ('Pedido', 1, 1),
        ('Egresos', 1, 1),
        ('Cuentas x Pagar', 1, 1),
        ('NA',1, 1);
        ");




        
    }
}

