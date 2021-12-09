<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblGeneroSchema;

class TblGeneroModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblGeneroSchema::class);
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

