<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\BackgroundWorkersSchema;

class BackgroundWorkersModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, BackgroundWorkersSchema::class);
	}	
}
