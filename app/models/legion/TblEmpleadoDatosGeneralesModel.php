<?php

namespace simplerest\models\legion;


use simplerest\models\MyModel;
use simplerest\schemas\legion\TblEmpleadoDatosGeneralesSchema;

class TblEmpleadoDatosGeneralesModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblEmpleadoDatosGeneralesSchema::class);
	}	
}

