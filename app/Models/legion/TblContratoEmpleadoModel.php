<?php

namespace Boctulus\Simplerest\Models\legion;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\legion\TblContratoEmpleadoSchema;

class TblContratoEmpleadoModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblContratoEmpleadoSchema::class);
	}	
}

