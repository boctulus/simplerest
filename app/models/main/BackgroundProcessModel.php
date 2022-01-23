<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\BackgroundProcessSchema;

class BackgroundProcessModel extends MyModel
{
	protected $createdAt = 'created_at';

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, BackgroundProcessSchema::class);
	}	
}

