<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblNotaCredito implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        
        ///Nombre Tabla
        $table = ('tbl_nota_credito');

        ///Nomenclartura Tabla
        $nom = 'nct_';
        $nomInt = $nom . 'int';
        $nomVar = $nom . 'var';
        $nomDec = $nom . 'dec';
        $nomLon = $nom . 'lon';
        $nomDat = $nom . 'dat';
        $nomBol = $nom . 'bol';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')
        
        ///Campo primare key tabla 
        ->integer($nomInt.'Id')->auto()->pri()

        ///Campos tabla 
        ->varchar($nomVar.'NumeroDocumento', 20)->comment('hashed')
        ->decimal($nomDec.'CantidadTotal' , 18,2)->comment('hashed')
        ->decimal($nomDec.'Bruto' , 18,2)->comment('hashed')
        ->decimal($nomDec.'Descuento' , 18,2)->comment('hashed')
        ->decimal($nomDec.'Valor', 18,2)->comment('hashed')
        ->decimal($nomDec.'Iva', 18,2)->comment('hashed')
        ->decimal($nomDec.'Ica', 18,2)->comment('hashed')
        ->decimal($nomDec.'Retencion', 18,2)->comment('hashed')
        ->decimal($nomDec.'ReteIva', 18,2)->comment('hashed')
        ->date($nomDat.'Fecha')->comment('hashed')
        ->decimal($nomDat.'Neto' , 18,2)->comment('hashed')
        ->decimal($nomDat.'PorceRetefuente', 18,2)->comment('hashed')
        ->integer($nomInt.'TopeRetefuente')->comment('hashed')
        ->decimal($nomDec.'PorceReteiva')->comment('hashed')
        ->integer($nomInt.'TopeReteiva')->comment('hashed')
        ->decimal($nomDec.'PorceIca')->comment('hashed')
        ->integer($nomInt.'TopeReteIca')->comment('hashed')
        ->longtext($nomLon.'Nota')->comment('hashed')
        ->tinyint($nomBol.'Cruzado')->comment('hashed')
        ->tinyint($nomBol.'EnviadoDian')->comment('hashed')
        
        ///Campos tabla  constantes y foreign de otras tablas 
        ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->varchar('fac_varNroDocumento' ,20)
        ->integer('fac_intIdFactura')
        ->integer('cen_intIdCentrocostos')
        ->integer('doc_intIdDocumento')
        ->integer('cse_intIdConsecutivo')
        ->integer('per_intIdPersona')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable() 
    
        ///foreign --- references Tabla tbl_usuario usu_intIdCreador
        ->foreign('usu_intIdCreador')
        ->references('usu_intId')
        ->on('tbl_usuario')

        ///foreign --- references Tabla tbl_usuario usu_intIdActualizador
        ->foreign('usu_intIdActualizador')
        ->references('usu_intId')
        ->on('tbl_usuario')  

        ///foreign --- references Tabla tbl_centro_costos
        ->foreign('cen_intIdCentrocostos')
        ->references('cco_intId')
        ->on('tbl_centro_costos')  
        
        ///foreign --- references Tabla tbl_consecutivo
        ->foreign('cse_intIdConsecutivo')
        ->references('cse_intId')
        ->on('tbl_consecutivo')  

        ///foreign --- references Tabla tbl_documento
        ->foreign('doc_intIdDocumento')
        ->references('doc_intId')
        ->on('tbl_documento')  

        ///foreign --- references Tabla tbl_factura
        ->foreign('fac_intIdFactura')
        ->references('fac_intId')
        ->on('tbl_factura')  

        ///foreign --- references Tabla tbl_persona
        ->foreign('per_intIdPersona')
        ->references('per_intId')
        ->on('tbl_persona');

        //Creacion de tabla
        $res = $sc->create();
    }
}

