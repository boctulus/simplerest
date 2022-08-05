<?php

namespace simplerest\models\mpo;


use simplerest\models\MyModel;
use simplerest\schemas\mpo\OrgVinculPersonalEntidadSchema;

class OrgVinculPersonalEntidadModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, OrgVinculPersonalEntidadSchema::class);
	}	
}

