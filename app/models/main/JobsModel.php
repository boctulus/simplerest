<?php

namespace Boctulus\Simplerest\Models\main;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\JobsSchema;

class JobsModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, JobsSchema::class);
	}	
}

