<?php

namespace simplerest\models\eb;


use simplerest\models\MyModel;
use simplerest\schemas\eb\MonedaSchema;

class MonedaModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, MonedaSchema::class);
	}	
}

