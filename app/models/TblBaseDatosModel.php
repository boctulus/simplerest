<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\TblBaseDatosSchema;

class TblBaseDatosModel extends Model
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblBaseDatosSchema());
	}	

	function onCreated(array &$data, $last_inserted_id)
	{
		// ejecutar el SP que crea la DB
	}
}

