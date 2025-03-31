<?php

namespace Boctulus\Simplerest\Models\main;

use MakeCommand;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Schemas\main\TblBaseDatosSchema;
use Boctulus\Simplerest\Controllers\MigrationsController;


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

		$tenant = $db_name;
		
		$folder = 'usuario';
		$mgr_o->migrate("--dir=$folder", "--to=$tenant");

        // $folder = 'compania'; 
        // $mgr_o->migrate("--dir=$folder", "--to=$tenant");

		
		/*
			Creo schemas y modelos
		*/

		$mk = MakeCommand::class;
		$mk_o = new $mk();

		StdOut::hideResponse();

		$mk_o->any("all", "-s", "-m", "--from:$tenant");

	}
}

