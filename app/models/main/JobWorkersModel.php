<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\JobWorkersSchema;

class JobWorkersModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, JobWorkersSchema::class);
	}	
}

