<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\legion\TblGeneroSchema;

class TblGeneroModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblGeneroSchema());
	}	

	function onCreating(array &$data)
	{
		//$this->dontExec();	
	}

	function onCreated(array &$data, $last_inserted_id)
	{
		dd($this->dd(true), 'SQL');
		dd($this->getLastBindingParamters(), 'PARAMETERS');
	}
}

