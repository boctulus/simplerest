<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\Model;
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

	// está deshabilitado (notar el "__" delante)
	function onCreated(array &$data, $last_inserted_id)
	{
		/*
			Creo la DB
		*/

		DB::getDefaultConnection();

		$db_name = $data['dba_varNombre'];
		
		$ok = Model::query("CREATE DATABASE $db_name;");
		

		/*
			Creo las tablas
		*/

		$mgr = MigrationsController::class;

        $folder = 'compania'; 
        $tenant = $db_name;

		StdOut::hideResponse();

        $mgr->migrate("--dir=$folder", "--to=$tenant");

		
		/*
			Creo schemas y modelos
		*/

		$mk = MakeControllerBase::class;

		StdOut::hideResponse();

		$mk->any("all", "-s", "-m", "--from:$tenant");

	}
}

