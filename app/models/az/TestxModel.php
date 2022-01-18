<?php

namespace simplerest\models\az;


use simplerest\models\MyModel;
use simplerest\schemas\az\TestxSchema;

class TestxModel extends MyModel
{
	protected $createdAt = null;

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TestxSchema::class);
	}	
}

