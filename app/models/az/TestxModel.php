<?php

namespace Boctulus\Simplerest\Models\az;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\az\TestxSchema;

class TestxModel extends MyModel
{
	protected $createdAt = null;

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TestxSchema::class);
	}	
}

