<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\libs\Config;
use simplerest\libs\DB;
use simplerest\core\MakeControllerBase;
use simplerest\controllers\MigrationsController;
use simplerest\libs\StdOut;
use simplerest\schemas\main\TblBaseDatosSchema;


class TblBaseDatosModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblBaseDatosSchema::class);
	}	

	// chequear no esté deshabilitado (con un "__" delante)
	function onCreated(array &$data, $last_inserted_id)
	{
		/*
			Creo la DB
		*/

		DB::getDefaultConnection();

		$db_name = $data['dba_varNombre'];
		
		/* usar backticks */
		$ok = DB::statement("CREATE DATABASE `$db_name`;");  
		
		if (!$ok){
			throw new \Exception("Error trying to create $db_name");
		}		

		/*
			Ahora debo *registrar* las conexiones (que incluyen a la nueva DB en el config)
		*/

		Config::set('db_connections', get_db_connections(true));
		

		/*
			Creo las tablas
		*/

		$mgr   = MigrationsController::class;
		$mgr_o = new $mgr();
				
		StdOut::hideResponse();

        $folder = 'usuario'; 
        $tenant = $db_name;

        $mgr_o->migrate("--dir=$folder", "--to=$tenant");

		
		/*
			Creo schemas y modelos
		*/

		$mk = MakeControllerBase::class;
		$mk_o = new $mk();

		StdOut::hideResponse();

		$mk_o->any("all", "-s", "-m", "--from:$tenant");

	}
}
