<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\schemas\main\JobProcessSchema;

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

