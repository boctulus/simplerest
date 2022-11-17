<?php

namespace simplerest\models\parts;

use simplerest\models\MyModel;
use simplerest\schemas\parts\PartNumbersSchema;

class PartNumbersModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, PartNumbersSchema::class);
	}	
}

