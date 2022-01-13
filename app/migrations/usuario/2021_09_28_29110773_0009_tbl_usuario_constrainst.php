<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;


class TblArlConstrainst30 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        DB::disableForeignKeyConstraints();

        $tableUsuario = 'tbl_usuario'; 
        $columnaUsuCreador = 'usu_intIdCreador'; 
        $columnaUsuActualizador = 'usu_intIdActualizador'; 
        $columnaIdUsuario = 'usu_intId';



        Model::query("ALTER TABLE tbl_arl  
            ADD CONSTRAINT FK_arl_IdCreador 
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_arl  
            ADD CONSTRAINT FK_arl_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador.") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");    

        Model::query("ALTER TABLE tbl_operador_pila  
            ADD CONSTRAINT FK_ope_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_operador_pila  
            ADD CONSTRAINT FK_ope_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_empresa  
            ADD CONSTRAINT FK_emp_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_empresa  
            ADD CONSTRAINT FK_emp_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_cargo  
            ADD CONSTRAINT FK_car_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_cargo  
            ADD CONSTRAINT FK_car_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_tipo_documento  
            ADD CONSTRAINT FK_tip_IdCreador
            FOREIGN KEY (".$columnaUsuCreador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        Model::query("ALTER TABLE tbl_tipo_documento  
            ADD CONSTRAINT FK_tip_IdActualizador 
            FOREIGN KEY (".$columnaUsuActualizador .") 
            REFERENCES ".$tableUsuario." (".$columnaIdUsuario.");
        ");

        DB::enableForeignKeyConstraints();

    }
}

