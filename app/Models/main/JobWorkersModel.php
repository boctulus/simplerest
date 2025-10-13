<?php

namespace Boctulus\Simplerest\Models\main;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\JobWorkersSchema;

class JobWorkersModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, JobWorkersSchema::class);
	}	
}

