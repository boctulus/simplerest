<?php

namespace simplerest\models\az;


use simplerest\models\MyModel;
use simplerest\schemas\az\JobTblSchema;

class JobTblModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, JobTblSchema::class);
	}	
}

