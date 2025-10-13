<?php

namespace Boctulus\Simplerest\Models\legion;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\legion\TblEmpleadoDatosGeneralesSchema;

class TblEmpleadoDatosGeneralesModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblEmpleadoDatosGeneralesSchema::class);
	}	
}

