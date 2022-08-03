<?php

namespace simplerest\models\eb;


use simplerest\models\MyModel;
use simplerest\schemas\eb\SucursalSchema;

class SucursalModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, SucursalSchema::class);
	}	
}

