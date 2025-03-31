<?php

namespace Boctulus\Simplerest\Models\edu;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\edu\CoursesSchema;

class CoursesModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, CoursesSchema::class);
	}	
}

