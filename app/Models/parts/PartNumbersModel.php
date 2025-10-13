<?php

namespace Boctulus\Simplerest\Models\parts;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\parts\PartNumbersSchema;

class PartNumbersModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, PartNumbersSchema::class);
	}	
}

