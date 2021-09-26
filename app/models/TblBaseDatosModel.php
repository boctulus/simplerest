<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\TblBaseDatosSchema;

class TblBaseDatosModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblBaseDatosSchema());
	}	

	function onCreated(array &$data, $last_inserted_id)
	{
		/*
			Ejecutar el SP que crea la DB o ...
		*/

		$db_name = $data['dba_varNombre'];

		Model::query("CREATE DATABASE $db_name;");
	}
}

