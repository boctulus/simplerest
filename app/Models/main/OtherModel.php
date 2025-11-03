<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;

class OtherModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $table_name = 'other_model';

    function __construct(bool $connect = false){
        parent::__construct($connect);
	}	
}

