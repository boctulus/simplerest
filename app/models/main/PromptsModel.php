<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\schemas\main\PromptsSchema;

class PromptsModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, PromptsSchema::class);
	}	
}

