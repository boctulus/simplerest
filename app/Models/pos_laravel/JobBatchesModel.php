<?php

namespace Boctulus\Simplerest\Models\pos_laravel;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\FriendlyposWeb\Schemas\JobBatchesSchema;

class JobBatchesModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, JobBatchesSchema::class);
	}	
}

