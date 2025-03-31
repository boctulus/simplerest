<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\JobProcessSchema;

class JobProcessModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, JobProcessSchema::class);
	}	
}

