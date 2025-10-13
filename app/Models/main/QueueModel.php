<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\QueueSchema;

class QueueModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, QueueSchema::class);
	}	
}

