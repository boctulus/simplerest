<?php

namespace Boctulus\Simplerest\Models\main;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\BackgroundProcessSchema;

class BackgroundProcessModel extends MyModel
{
	protected $createdAt = 'created_at';

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, BackgroundProcessSchema::class);
	}	
}

