<?php

namespace Boctulus\Simplerest\Models\edu;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\edu\CourseTagSchema;

class CourseTagModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, CourseTagSchema::class);
	}	
}

