<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;


class TblArlConstrainst30 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $tableUsuario = 'tbl_usuario'; 
        $columnaUsuCreador = 'usu_intIdCreador'; 
        $columnaUsuActualizador = 'usu_intIdActualizador'; 
        $columnaIdUsuario = 'usu_intId';

        DB::statement("ALTER TABLE tbl_arl  
            ADD CONSTRAINT FK_arl_IdCreador 
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::statement("ALTER TABLE tbl_arl  
            ADD CONSTRAINT FK_arl_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador.") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");    

        DB::statement("ALTER TABLE tbl_operador_pila  
            ADD CONSTRAINT FK_ope_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::statement("ALTER TABLE tbl_operador_pila  
            ADD CONSTRAINT FK_ope_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::statement("ALTER TABLE tbl_empresa  
            ADD CONSTRAINT FK_emp_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::statement("ALTER TABLE tbl_empresa  
            ADD CONSTRAINT FK_emp_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::statement("ALTER TABLE tbl_cargo  
            ADD CONSTRAINT FK_car_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::statement("ALTER TABLE tbl_cargo  
            ADD CONSTRAINT FK_car_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        // DB::statement("ALTER TABLE tbl_tipo_documento  
        //     ADD CONSTRAINT FK_tip_IdCreador
        //     FOREIGN KEY (".$columnaUsuCreador .") 
        //     REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        // ");

        // DB::statement("ALTER TABLE tbl_tipo_documento  
        //     ADD CONSTRAINT FK_tip_IdActualizador 
        //     FOREIGN KEY (".$columnaUsuActualizador .") 
        //     REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        // ");

    }
}

