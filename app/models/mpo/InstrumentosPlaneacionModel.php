<?php

namespace simplerest\models\mpo;


use simplerest\models\MyModel;
use simplerest\schemas\mpo\InstrumentosPlaneacionSchema;

class InstrumentosPlaneacionModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, InstrumentosPlaneacionSchema::class);
	}	
}

