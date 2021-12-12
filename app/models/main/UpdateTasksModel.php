<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\UpdateTasksSchema;

class UpdateTasksModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, UpdateTasksSchema::class);
	}	
}

