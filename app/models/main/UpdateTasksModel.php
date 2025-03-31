<?php

namespace Boctulus\Simplerest\Models\main;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\UpdateTasksSchema;

class UpdateTasksModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, UpdateTasksSchema::class);
	}	
}

