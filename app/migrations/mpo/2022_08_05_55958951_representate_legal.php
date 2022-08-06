<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class RepresentateLegal implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::setConnection('mpo');
		
		$table = new Schema('el_representate_legal');

        $table->increments();
        $table->string('tipo_doc', 20) ; // podria ser otra tabla o un ENUM
        $table->string('nro_doc', 25);  // podria ser un pasaporte que suelen incluir letras
        $table->string('departamento_exp', '30'); // podria ser otra tabla o un ENUM
        $table->string('municipio_exp', 50); // podria ser otra tabla o un ENUM
        $table->string('nombres', 80);
        $table->string('apellidos', 100);
        $table->dateTime('fecha_nacimiento');
        $table->string('genero', 15); // podria ser otra tabla o un ENUM
        $table->string('profesion_oficio', 40);
        $table->string('tarjeta_profesional', 20)->nullable();
        $table->string('estado_civil', 20); // podria ser otra tabla o un ENUM
        $table->string('estado_laboral', 20); // podria ser otra tabla o un ENUM
        $table->string('tel_fijo', 20)->nullable();
        $table->string('tel_celular', 20);
        $table->string('email'); //->unique()
        $table->string('direccion');
        $table->string('zona')->nullable();
        $table->string('barrio');
        $table->boolean('sabe_leer');
        $table->boolean('sabe_escribir');
        $table->int('el_nivel_escolaridad_id', 11)->index();
        $table->foreign('el_nivel_escolaridad_id')->references('id')->on('el_nivel_escolaridad')->onDelete('cascade');
        $table->timestamps();
		$table->create();		
    }

    public function down(){
        DB::setConnection('mpo');

        Schema::dropIfExists('el_representate_legal');
    }
}

