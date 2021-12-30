<?php

namespace simplerest\models\az;


use simplerest\models\MyModel;
use simplerest\schemas\az\UrSchema;

class UrModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, UrSchema::class);
	}	
}

